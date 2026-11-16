<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display the authenticated student's activity and audit logs.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $logType = $request->input('log_type');
        $action = $request->input('action');
        $timeframe = $request->input('timeframe', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');

        $baseQuery = SystemLog::forStudent($user);

        $logs = (clone $baseQuery)
            ->with(['user', 'subject'])
            ->latest('created_at')
            ->filterByType($logType)
            ->filterByAction($action)
            ->filterByDate($timeframe, $startDate, $endDate)
            ->search($search)
            ->paginate(15)
            ->withQueryString();

        // Student Activity Summary Metrics
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'today' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'applications' => (clone $baseQuery)->whereIn('log_type', ['Application', 'Scholar', 'Compliance', 'Welfare Case'])->count(),
            'security' => (clone $baseQuery)->whereIn('log_type', ['Authentication', 'Security', 'Student'])->count(),
        ];

        $availableTypes = [
            'Authentication' => 'Authentication & Sessions',
            'Application' => 'Scholarship Applications',
            'Compliance' => 'Compliance Submissions',
            'Scholar' => 'Scholarship & Renewals',
            'Welfare Case' => 'Welfare Cases',
            'Student' => 'Profile Updates',
            'Security' => 'Account Security',
        ];

        return view('student.activity_logs.index', compact(
            'logs',
            'stats',
            'logType',
            'action',
            'timeframe',
            'startDate',
            'endDate',
            'search',
            'availableTypes'
        ));
    }
}

