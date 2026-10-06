<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemLogController extends Controller
{
    /**
     * Display a listing of system activity and audit logs.
     */
    public function index(Request $request)
    {
        $logType = $request->input('log_type');
        $action = $request->input('action');
        $timeframe = $request->input('timeframe', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');

        $query = SystemLog::with(['user', 'subject'])
            ->latest('created_at')
            ->filterByType($logType)
            ->filterByAction($action)
            ->filterByDate($timeframe, $startDate, $endDate)
            ->search($search);

        $logs = $query->paginate(25)->withQueryString();

        // Statistical Counters
        $stats = [
            'total' => SystemLog::count(),
            'today' => SystemLog::whereDate('created_at', today())->count(),
            'auth' => SystemLog::where('log_type', 'Authentication')->count(),
            'management' => SystemLog::whereIn('log_type', ['Application', 'Scholarship', 'Welfare Case', 'Compliance', 'Scholar', 'Settings'])->count(),
        ];

        $availableTypes = [
            'Authentication' => 'Authentication & Access',
            'Scholarship' => 'Scholarship Programs',
            'Application' => 'Scholarship Applications',
            'Welfare Case' => 'Student Welfare Cases',
            'Compliance' => 'Compliance Tracking',
            'Scholar' => 'Scholars & Grantees',
            'Student' => 'Student Profiles',
            'Settings' => 'System & Branding Settings',
            'Export' => 'Data Exports & Reports',
            'Security' => 'Security & Auditing',
        ];

        $availableActions = SystemLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();

        return view('admin.system_logs.index', compact(
            'logs',
            'stats',
            'logType',
            'action',
            'timeframe',
            'startDate',
            'endDate',
            'search',
            'availableTypes',
            'availableActions'
        ));
    }

    /**
     * Retrieve single log details (JSON).
     */
    public function show(SystemLog $systemLog)
    {
        $systemLog->load(['user', 'subject']);
        
        return response()->json([
            'id' => $systemLog->id,
            'log_type' => $systemLog->log_type,
            'action' => $systemLog->action,
            'description' => $systemLog->description,
            'user' => $systemLog->user ? [
                'name' => $systemLog->user->full_name,
                'email' => $systemLog->user->email,
                'role' => $systemLog->user->role,
            ] : null,
            'subject_type' => $systemLog->subject_type ? class_basename($systemLog->subject_type) : null,
            'subject_id' => $systemLog->subject_id,
            'properties' => $systemLog->properties,
            'ip_address' => $systemLog->ip_address,
            'user_agent' => $systemLog->user_agent,
            'created_at' => $systemLog->created_at->format('F d, Y h:i:s A'),
            'created_at_human' => $systemLog->created_at->diffForHumans(),
        ]);
    }

    /**
     * Export filtered system logs to CSV for compliance and external auditing.
     */
    public function export(Request $request): StreamedResponse
    {
        $logType = $request->input('log_type');
        $action = $request->input('action');
        $timeframe = $request->input('timeframe', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');

        $query = SystemLog::with(['user', 'subject'])
            ->latest('created_at')
            ->filterByType($logType)
            ->filterByAction($action)
            ->filterByDate($timeframe, $startDate, $endDate)
            ->search($search);

        // Record the export in the system logs
        SystemLog::record(
            'Export',
            'export_csv',
            'Exported system audit logs to CSV',
            null,
            ['filters' => $request->only(['log_type', 'action', 'timeframe', 'search'])]
        );

        $filename = 'CSU_Lallo_OSDW_System_Logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Log ID',
                'Timestamp',
                'Log Category',
                'Action Type',
                'Description',
                'User Full Name',
                'User Email',
                'User Role',
                'Affected Entity',
                'Entity ID',
                'IP Address',
                'User Agent',
                'Metadata / Changes (JSON)',
            ]);

            $query->chunk(200, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $log->log_type,
                        $log->action,
                        $log->description,
                        $log->user ? $log->user->full_name : 'System / Guest',
                        $log->user ? $log->user->email : 'N/A',
                        $log->user ? ucfirst($log->user->role) : 'N/A',
                        $log->subject_type ? class_basename($log->subject_type) : 'N/A',
                        $log->subject_id ?? 'N/A',
                        $log->ip_address ?? 'N/A',
                        $log->user_agent ?? 'N/A',
                        $log->properties ? json_encode($log->properties, JSON_UNESCAPED_SLASHES) : '',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Purge logs older than specified days (Admin Only Retention Policy).
     */
    public function clearOld(Request $request)
    {
        $validated = $request->validate([
            'days' => 'required|integer|in:30,60,90,180,365',
        ]);

        $cutoff = now()->subDays((int)$validated['days']);
        $deletedCount = SystemLog::where('created_at', '<', $cutoff)->delete();

        SystemLog::record(
            'Security',
            'purge_logs',
            "Purged {$deletedCount} system logs older than {$validated['days']} days.",
            null,
            ['days' => $validated['days'], 'purged_count' => $deletedCount]
        );

        return redirect()->route('admin.system-logs.index')
            ->with('success', "Successfully purged {$deletedCount} historical system logs older than {$validated['days']} days.");
    }
}
