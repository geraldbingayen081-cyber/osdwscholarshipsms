<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ComplianceRequest;
use App\Models\ComplianceRequirement;
use App\Models\Scholar;
use App\Models\ScholarCompliance;
use App\Models\ScholarComplianceDocument;
use App\Models\Scholarship;
use App\Notifications\ComplianceDocumentVerifiedNotification;
use App\Notifications\ComplianceRequestedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminComplianceController extends Controller
{
    /**
     * Display a listing of all Compliance Requests and summary statistics.
     */
    public function index(Request $request)
    {
        $scholarshipId = $request->get('scholarship_id');
        $schoolYear = $request->get('school_year');
        $status = $request->get('status');
        $search = $request->get('search');

        $query = ComplianceRequest::with(['scholarship', 'creator', 'requirements', 'scholarCompliances']);

        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        if ($schoolYear) {
            $query->where('school_year', $schoolYear);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('school_year', 'like', "%{$search}%")
                  ->orWhere('semester', 'like', "%{$search}%")
                  ->orWhereHas('scholarship', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $complianceRequests = $query->latest()->paginate(10)->withQueryString();

        // Statistics
        $totalRequests = ComplianceRequest::count();
        $activeRequests = ComplianceRequest::where('status', 'active')->count();
        $totalCompliancesAssigned = ScholarCompliance::count();
        $pendingReviewsCount = ScholarComplianceDocument::where('verification_status', 'pending')->count();
        $completedCompliancesCount = ScholarCompliance::where('status', 'completed')->count();

        $stats = [
            'total_requests' => $totalRequests,
            'active_requests' => $activeRequests,
            'assigned_scholars' => $totalCompliancesAssigned,
            'pending_reviews' => $pendingReviewsCount,
            'completed' => $completedCompliancesCount,
        ];

        $scholarships = Scholarship::orderBy('name')->get();
        $schoolYears = ComplianceRequest::select('school_year')->distinct()->pluck('school_year');

        return view('admin.compliance.index', compact(
            'complianceRequests',
            'stats',
            'scholarships',
            'schoolYears',
            'scholarshipId',
            'schoolYear',
            'status',
            'search'
        ));
    }

    /**
     * Store and publish a newly created Compliance Request, auto-assigning to active scholars.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scholarship_id' => ['required', 'exists:scholarships,id'],
            'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
            'semester' => ['required', 'string', Rule::in(['1st Semester', '2nd Semester', 'Summer Term'])],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'title' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string'],
            'requirements' => ['required', 'array', 'min:1'],
            'requirements.*.name' => ['required', 'string', 'max:255'],
            'requirements.*.instruction' => ['nullable', 'string'],
            'requirements.*.is_required' => ['nullable', 'boolean'],
        ], [
            'scholarship_id.required' => 'Please select a Scholarship Program.',
            'school_year.regex' => 'School Year must be in XXXX-XXXX format (e.g. 2026-2027).',
            'deadline.after_or_equal' => 'Deadline cannot be set in the past.',
            'requirements.min' => 'Please include at least one requirement for this compliance request.',
            'requirements.*.name.required' => 'Each requirement item must have a valid title/name.',
        ]);

        $scholarship = Scholarship::findOrFail($validated['scholarship_id']);

        $title = !empty($validated['title']) 
            ? $validated['title'] 
            : "{$scholarship->name} — {$validated['semester']} AY {$validated['school_year']} Compliance Request";

        DB::transaction(function () use ($validated, $scholarship, $title, &$complianceRequest, &$assignedCount) {
            // Find or sync academic year
            $ay = AcademicYear::firstOrCreate(
                ['name' => "AY {$validated['school_year']}"],
                ['status' => 'inactive']
            );

            $complianceRequest = ComplianceRequest::create([
                'scholarship_id' => $scholarship->id,
                'academic_year_id' => $ay->id,
                'school_year' => $validated['school_year'],
                'semester' => $validated['semester'],
                'title' => $title,
                'instructions' => $validated['instructions'] ?? null,
                'deadline' => $validated['deadline'],
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // Create Compliance Requirements
            foreach ($validated['requirements'] as $reqData) {
                ComplianceRequirement::create([
                    'compliance_request_id' => $complianceRequest->id,
                    'name' => $reqData['name'],
                    'instruction' => $reqData['instruction'] ?? null,
                    'is_required' => isset($reqData['is_required']) ? (bool) $reqData['is_required'] : true,
                ]);
            }

            // Automatically identify all active scholars under this scholarship program
            $activeScholars = Scholar::where('scholarship_id', $scholarship->id)
                ->where('status', 'active')
                ->with('student.user')
                ->get();

            $assignedCount = $activeScholars->count();

            foreach ($activeScholars as $scholar) {
                ScholarCompliance::create([
                    'compliance_request_id' => $complianceRequest->id,
                    'scholar_id' => $scholar->id,
                    'status' => 'not_submitted',
                ]);

                // Dispatch in-app notification to student
                if ($scholar->student && $scholar->student->user) {
                    $scholar->student->user->notify(new ComplianceRequestedNotification($complianceRequest));
                }
            }
        });

        return redirect()->route('admin.compliance.show', $complianceRequest->id)
            ->with('success', "Compliance Request successfully created and dispatched to {$assignedCount} active scholars.");
    }

    /**
     * Display a detailed monitoring dashboard for a specific Compliance Request.
     */
    public function show(ComplianceRequest $complianceRequest, Request $request)
    {
        $statusFilter = $request->get('status');
        $search = $request->get('search');

        $complianceRequest->load(['scholarship', 'creator', 'requirements']);

        $query = ScholarCompliance::where('compliance_request_id', $complianceRequest->id)
            ->with(['scholar.student.user', 'documents.requirement']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->whereHas('scholar.student', function ($sq) use ($search) {
                $sq->where('student_number', 'like', "%{$search}%")
                   ->orWhere('course', 'like', "%{$search}%")
                   ->orWhereHas('user', function ($uq) use ($search) {
                       $uq->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                   });
            });
        }

        $scholarCompliances = $query->paginate(15)->withQueryString();

        // Check if compliance request is past deadline and update overdue status dynamically
        if ($complianceRequest->isPastDeadline()) {
            foreach ($scholarCompliances as $sc) {
                if (in_array($sc->status, ['not_submitted', 'partially_submitted'])) {
                    $sc->recalculateStatus();
                }
            }
        }

        // Summary counts for this specific request
        $stats = [
            'total_assigned' => ScholarCompliance::where('compliance_request_id', $complianceRequest->id)->count(),
            'not_submitted' => ScholarCompliance::where('compliance_request_id', $complianceRequest->id)->where('status', 'not_submitted')->count(),
            'partially_submitted' => ScholarCompliance::where('compliance_request_id', $complianceRequest->id)->where('status', 'partially_submitted')->count(),
            'under_review' => ScholarCompliance::where('compliance_request_id', $complianceRequest->id)->whereIn('status', ['submitted', 'under_review'])->count(),
            'needs_correction' => ScholarCompliance::where('compliance_request_id', $complianceRequest->id)->where('status', 'needs_correction')->count(),
            'completed' => ScholarCompliance::where('compliance_request_id', $complianceRequest->id)->where('status', 'completed')->count(),
            'overdue' => ScholarCompliance::where('compliance_request_id', $complianceRequest->id)->where('status', 'overdue')->count(),
        ];

        return view('admin.compliance.show', compact(
            'complianceRequest',
            'scholarCompliances',
            'stats',
            'statusFilter',
            'search'
        ));
    }

    /**
     * Verify, request correction, or reject an individual compliance document.
     */
    public function verifyDocument(Request $request, ScholarComplianceDocument $document)
    {
        $validated = $request->validate([
            'verification_status' => ['required', Rule::in(['verified', 'needs_correction', 'rejected'])],
            'admin_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['verification_status'] === 'needs_correction' && empty($validated['admin_remarks'])) {
            return back()->with('error', 'Please provide a reason or correction instruction for the student.');
        }

        $document->update([
            'verification_status' => $validated['verification_status'],
            'admin_remarks' => $validated['admin_remarks'] ?? null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Recalculate overall scholar compliance status
        $scholarCompliance = $document->scholarCompliance;
        $overallStatus = $scholarCompliance->recalculateStatus();

        // Notify student of document status
        if ($scholarCompliance->scholar && $scholarCompliance->scholar->student && $scholarCompliance->scholar->student->user) {
            $scholarCompliance->scholar->student->user->notify(new ComplianceDocumentVerifiedNotification($document));
        }

        $statusName = match ($validated['verification_status']) {
            'verified' => 'verified',
            'needs_correction' => 'marked as needing correction',
            'rejected' => 'rejected',
        };

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Document {$statusName}.",
                'document' => $document,
                'overall_status' => $overallStatus,
            ]);
        }

        return back()->with('success', "Document for '{$document->requirement->name}' was {$statusName}.");
    }

    /**
     * Preview an uploaded compliance document directly in browser.
     */
    public function viewDocument(ScholarComplianceDocument $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'The requested document file was not found on storage disk.');
        }

        $path = Storage::disk('public')->path($document->file_path);
        
        $extension = strtolower(pathinfo($document->original_filename, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => Storage::disk('public')->mimeType($document->file_path) ?? 'application/pdf',
        };

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->original_filename) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Extend or reopen the submission deadline for a compliance request.
     */
    public function extendDeadline(Request $request, ComplianceRequest $complianceRequest)
    {
        $validated = $request->validate([
            'deadline' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $complianceRequest->update([
            'deadline' => $validated['deadline'],
            'status' => 'extended',
        ]);

        // Recalculate overdue statuses
        foreach ($complianceRequest->scholarCompliances as $sc) {
            if ($sc->status === 'overdue') {
                $sc->recalculateStatus();
            }
        }

        return back()->with('success', "Compliance submission deadline has been extended to " . date('F d, Y', strtotime($validated['deadline'])) . ".");
    }

    /**
     * Delete a compliance request and cascade delete all assignments.
     */
    public function destroy(ComplianceRequest $complianceRequest)
    {
        $title = $complianceRequest->title;
        $complianceRequest->delete();

        return redirect()->route('admin.compliance.index')
            ->with('success', "Compliance Request '{$title}' was deleted successfully.");
    }
}
