<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    /**
     * Display a listing of student applications with search & filter controls.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $scholarshipId = $request->get('scholarship_id');
        $academicYearId = $request->get('academic_year_id');
        $search = $request->get('search');

        $query = Application::with(['student.user', 'scholarship.academicYear', 'documents']);

        if ($status) {
            $query->where('status', $status);
        } else {
            // Default view: exclude approved applications as they have moved to Active Scholars
            $query->where('status', '!=', 'approved');
        }

        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        if ($academicYearId) {
            $cleanSY = str_replace('AY ', '', $academicYearId);
            $query->whereHas('scholarship', function ($q) use ($academicYearId, $cleanSY) {
                $q->where('academic_year_id', $academicYearId)
                  ->orWhere('school_year', $cleanSY)
                  ->orWhere('school_year', $academicYearId)
                  ->orWhereHas('academicYear', function ($ayq) use ($academicYearId, $cleanSY) {
                      $ayq->where('name', $academicYearId)
                          ->orWhere('name', "AY {$cleanSY}")
                          ->orWhere('name', 'like', "%{$cleanSY}%")
                          ->orWhere('id', $academicYearId);
                  });
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($uq) use ($search) {
                    $uq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                })->orWhereHas('student', function ($sq) use ($search) {
                    $sq->where('student_number', 'like', "%{$search}%")
                       ->orWhere('course', 'like', "%{$search}%");
                })->orWhereHas('scholarship', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $applications = $query->latest('submitted_at')->paginate(10);

        // General counters
        $stats = [
            'total' => Application::count(),
            'submitted' => Application::where('status', 'submitted')->count(),
            'under_review' => Application::where('status', 'under_review')->count(),
            'incomplete' => Application::where('status', 'incomplete')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        $scholarships = Scholarship::orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.applications.index', compact(
            'applications',
            'stats',
            'scholarships',
            'academicYears',
            'status',
            'scholarshipId',
            'academicYearId',
            'search'
        ));
    }

    /**
     * Display a specific application for administrative review.
     */
    public function show(Application $application)
    {
        $application->load([
            'student.user',
            'scholarship.academicYear',
            'scholarship.semester',
            'scholarship.requirements',
            'documents.requirement',
            'scholar',
        ]);

        return view('admin.applications.show', compact('application'));
    }

    /**
     * Update application status (e.g. Under Review, Incomplete, Approved, Rejected).
     */
    public function updateStatus(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['submitted', 'under_review', 'incomplete', 'approved', 'rejected'])],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $scholar = null;

        DB::transaction(function () use ($application, $validated, &$scholar) {
            $oldStatus = $application->status;
            $application->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
            ]);

            // If status changed to Approved, automatically register/activate Scholar record
            if ($validated['status'] === 'approved') {
                $scholar = Scholar::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'student_id' => $application->student_id,
                        'scholarship_id' => $application->scholarship_id,
                        'status' => 'active',
                        'approved_at' => now(),
                    ]
                );
            } else {
                Scholar::where('application_id', $application->id)->delete();
            }

            // Notify Student User
            if ($application->student && $application->student->user) {
                $application->student->user->notify(new ApplicationStatusUpdated($application, $validated['remarks']));
            }

            \App\Models\SystemLog::record(
                'Application',
                'status_change',
                "Updated application #APP-{$application->id} for " . ($application->student->user->full_name ?? 'Student') . " to " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".",
                $application,
                [
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status'],
                    'remarks' => $validated['remarks'] ?? null,
                    'scholar_created' => (bool)$scholar,
                ]
            );
        });

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            $stats = [
                'total' => Application::count(),
                'submitted' => Application::where('status', 'submitted')->count(),
                'under_review' => Application::where('status', 'under_review')->count(),
                'incomplete' => Application::where('status', 'incomplete')->count(),
                'approved' => Application::where('status', 'approved')->count(),
                'rejected' => Application::where('status', 'rejected')->count(),
            ];

            $statusVal = $application->status instanceof \App\Enums\ApplicationStatus ? $application->status->value : (string) $application->status;
            return response()->json([
                'success' => true,
                'message' => 'Application for ' . ($application->student->user->full_name ?? 'Student') . ' updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . ($validated['status'] === 'approved' ? ' and student moved to Active Scholars.' : '.'),
                'status' => $statusVal,
                'status_label' => ucfirst(str_replace('_', ' ', $statusVal)),
                'scholar_id' => $scholar ? $scholar->id : null,
                'scholar_url' => $scholar ? route('admin.scholars.show', $scholar->id) : null,
                'stats' => $stats,
            ]);
        }

        return redirect()->route('admin.applications.show', $application->id)
            ->with('success', 'Application status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
    }

    /**
     * Update status for multiple selected student applications (Bulk Action).
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'application_ids' => ['required', 'array', 'min:1'],
            'application_ids.*' => ['integer', 'exists:applications,id'],
            'status' => ['required', Rule::in(['submitted', 'under_review', 'incomplete', 'approved', 'rejected'])],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $updatedCount = 0;

        DB::transaction(function () use ($validated, &$updatedCount) {
            $applications = Application::whereIn('id', $validated['application_ids'])->get();

            foreach ($applications as $app) {
                $app->update([
                    'status' => $validated['status'],
                    'remarks' => $validated['remarks'] ?? null,
                ]);

                if ($validated['status'] === 'approved') {
                    Scholar::updateOrCreate(
                        ['application_id' => $app->id],
                        [
                            'student_id' => $app->student_id,
                            'scholarship_id' => $app->scholarship_id,
                            'status' => 'active',
                            'approved_at' => now(),
                        ]
                    );
                } else {
                    Scholar::where('application_id', $app->id)->delete();
                }

                if ($app->student && $app->student->user) {
                    $app->student->user->notify(new ApplicationStatusUpdated($app, $validated['remarks'] ?? null));
                }

                $updatedCount++;
            }

            \App\Models\SystemLog::record(
                'Application',
                'bulk_status_change',
                "Bulk updated {$updatedCount} application(s) to " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".",
                null,
                [
                    'application_ids' => $validated['application_ids'],
                    'new_status' => $validated['status'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );
        });

        $stats = [
            'total' => Application::count(),
            'submitted' => Application::where('status', 'submitted')->count(),
            'under_review' => Application::where('status', 'under_review')->count(),
            'incomplete' => Application::where('status', 'incomplete')->count(),
            'approved' => Application::where('status', 'approved')->count(),
            'rejected' => Application::where('status', 'rejected')->count(),
        ];

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} student application(s) to " . ucfirst(str_replace('_', ' ', $validated['status'])) . ($validated['status'] === 'approved' ? ' and enrolled into Active Scholars.' : '.'),
                'updated_count' => $updatedCount,
                'stats' => $stats,
            ]);
        }

        return redirect()->route('admin.applications.index')
            ->with('success', "Successfully updated {$updatedCount} application(s) to " . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
    }

    /**
     * Update single document status (Verified or Needs Resubmission).
     */
    public function verifyDocument(Request $request, ApplicationDocument $document)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['verified', 'needs_resubmission', 'pending'])],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $autoApproved = false;

        DB::transaction(function () use ($document, $validated, &$autoApproved) {
            $document->update([
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
                'verified_at' => $validated['status'] === 'verified' ? now() : null,
            ]);

            $application = $document->application()->with(['documents', 'scholarship.requirements', 'student.user'])->first();

            // Notify student user about this document's status
            if ($application && $application->student && $application->student->user) {
                $application->student->user->notify(new DocumentStatusUpdated($document, $validated['status'], $validated['remarks'] ?? null));
            }

            if ($application) {
                if ($validated['status'] === 'needs_resubmission') {
                    // If document requires resubmission, update application status to 'incomplete'
                    $application->update(['status' => 'incomplete']);
                    Scholar::where('application_id', $application->id)->delete();
                } elseif ($validated['status'] === 'verified') {
                    // Check if all required documents and all submitted documents are now verified
                    $docs = $application->documents;
                    $requiredReqIds = $application->scholarship
                        ? $application->scholarship->requirements
                            ->where('requirement_type', 'document')
                            ->where('status', 'active')
                            ->where('is_required', true)
                            ->pluck('id')
                        : collect();

                    $allSubmittedVerified = $docs->isNotEmpty() && $docs->every(fn ($d) => $d->status === 'verified');
                    $allRequiredVerified = $requiredReqIds->every(function ($reqId) use ($docs) {
                        $doc = $docs->firstWhere('scholarship_requirement_id', $reqId);
                        return $doc && $doc->status === 'verified';
                    });

                    if ($allSubmittedVerified && $allRequiredVerified) {
                        $wasApproved = $application->status === 'approved';
                        $oldStatus = $application->status;

                        $application->update(['status' => 'approved']);

                        $scholar = Scholar::updateOrCreate(
                            ['application_id' => $application->id],
                            [
                                'student_id' => $application->student_id,
                                'scholarship_id' => $application->scholarship_id,
                                'status' => 'active',
                                'approved_at' => now(),
                            ]
                        );

                        if (!$wasApproved) {
                            $autoApproved = true;

                            if ($application->student && $application->student->user) {
                                $application->student->user->notify(
                                    new ApplicationStatusUpdated($application, 'All submitted requirement documents have been verified. Your application is now Approved!')
                                );
                            }

                            \App\Models\SystemLog::record(
                                'Application',
                                'auto_approved',
                                "Application #APP-{$application->id} for " . ($application->student->user->full_name ?? 'Student') . " was automatically Approved after all requirement documents were verified.",
                                $application,
                                [
                                    'old_status' => $oldStatus,
                                    'new_status' => 'approved',
                                    'scholar_id' => $scholar->id,
                                ]
                            );
                        }
                    } elseif ($application->status === 'incomplete' && !$docs->contains(fn ($d) => $d->status === 'needs_resubmission')) {
                        $application->update(['status' => 'under_review']);
                    }
                }
            }

            \App\Models\SystemLog::record(
                'Application',
                'verify_document',
                "Document '{$document->original_filename}' for Application #APP-{$document->application_id} marked as " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".",
                $document,
                [
                    'application_id' => $document->application_id,
                    'document_id' => $document->id,
                    'status' => $validated['status'],
                    'remarks' => $validated['remarks'] ?? null,
                ]
            );
        });

        $successMessage = $autoApproved
            ? 'All requirement documents verified! Application status automatically changed to Approved and student enrolled as an Active Scholar.'
            : 'Document verification status saved successfully.';

        return redirect()->route('admin.applications.show', $document->application_id)
            ->with('success', $successMessage);
    }

    /**
     * View uploaded application document file inline securely.
     */
    public function downloadDocument(ApplicationDocument $document)
    {
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found on storage server.');
        }

        $fullPath = Storage::disk('local')->path($document->file_path);
        
        $extension = strtolower(pathinfo($document->original_filename, PATHINFO_EXTENSION));
        $mimeType = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => Storage::disk('local')->mimeType($document->file_path) ?? 'application/pdf',
        };

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($document->original_filename) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
