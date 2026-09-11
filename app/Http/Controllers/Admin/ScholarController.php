<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholar;
use App\Models\ScholarRenewal;
use App\Models\Scholarship;
use App\Notifications\ScholarStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ScholarController extends Controller
{
    /**
     * Display a paginated listing of scholar grantees with scholarship program filtering.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $scholarshipId = $request->get('scholarship_id');
        $search = $request->get('search');

        $query = Scholar::with(['student.user', 'scholarship.academicYear', 'application.documents', 'renewals']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($uq) use ($search) {
                    $uq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('student', function ($sq) use ($search) {
                    $sq->where('student_number', 'like', "%{$search}%")
                       ->orWhere('course', 'like', "%{$search}%")
                       ->orWhere('college', 'like', "%{$search}%");
                })->orWhereHas('scholarship', function ($schq) use ($search) {
                    $schq->where('name', 'like', "%{$search}%")
                         ->orWhere('school_year', 'like', "%{$search}%");
                });
            });
        }

        $scholars = $query->latest('approved_at')->paginate(15)->withQueryString();

        // Counter Stats
        $stats = [
            'total' => Scholar::count(),
            'active' => Scholar::where('status', 'active')->count(),
            'completed' => Scholar::where('status', 'completed')->count(),
            'terminated' => Scholar::where('status', 'terminated')->count(),
        ];

        $scholarships = Scholarship::with('academicYear')->orderBy('name')->get();

        return view('admin.scholars.index', compact('scholars', 'stats', 'scholarships', 'status', 'scholarshipId', 'search'));
    }

    /**
     * Display details of a specific scholar profile including submitted requirements and renewals.
     */
    public function show(Scholar $scholar)
    {
        $scholar->load([
            'student.user',
            'scholarship.academicYear',
            'scholarship.semester',
            'application.documents.requirement',
        ]);

        return view('admin.scholars.show', compact('scholar'));
    }

    /**
     * Update scholar standing status. Supports AJAX inline updates.
     */
    public function updateStatus(Request $request, Scholar $scholar)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'completed', 'terminated'])],
        ]);

        $scholar->update([
            'status' => $validated['status'],
        ]);

        if ($scholar->student && $scholar->student->user) {
            $scholar->student->user->notify(new ScholarStatusUpdated($scholar));
        }

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            $stats = [
                'total' => Scholar::count(),
                'active' => Scholar::where('status', 'active')->count(),
                'completed' => Scholar::where('status', 'completed')->count(),
                'terminated' => Scholar::where('status', 'terminated')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Scholar standing for ' . ($scholar->student->user->full_name ?? 'Scholar') . ' updated to ' . ucfirst(str_replace('_', ' ', $scholar->status)) . '.',
                'status' => $scholar->status,
                'status_label' => ucfirst(str_replace('_', ' ', $scholar->status)),
                'stats' => $stats,
            ]);
        }

        return redirect()->route('admin.scholars.show', $scholar->id)
            ->with('success', 'Scholar status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
    }

    /**
     * Verify or flag a scholar's renewal document submission.
     */
    public function verifyRenewalDoc(Request $request, ScholarRenewal $renewal)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['verified', 'needs_resubmission', 'pending'])],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($renewal, $validated) {
            $renewal->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
                'verified_at' => $validated['status'] === 'verified' ? now() : null,
            ]);

            // If renewal document is verified, reactivate scholar standing to active
            if ($validated['status'] === 'verified') {
                $renewal->scholar->update(['status' => 'active']);
            }
        });

        return redirect()->route('admin.scholars.show', $renewal->scholar_id)
            ->with('success', 'Renewal document status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
    }

    /**
     * View uploaded scholar renewal document file inline securely.
     */
    public function downloadRenewalDoc(ScholarRenewal $renewal)
    {
        if (!Storage::disk('local')->exists($renewal->file_path)) {
            abort(404, 'Renewal document file not found on storage server.');
        }

        $fullPath = Storage::disk('local')->path($renewal->file_path);
        
        $extension = strtolower(pathinfo($renewal->original_filename, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => Storage::disk('local')->mimeType($renewal->file_path) ?? 'application/pdf',
        };

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($renewal->original_filename) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
