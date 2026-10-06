<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholar;
use App\Models\ScholarRenewal;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\SystemLog;
use App\Notifications\ScholarStatusUpdated;
use Carbon\Carbon;
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
        $students = Student::with('user')->get()->sortBy(fn($s) => $s->user?->last_name ?? $s->student_number)->values();

        return view('admin.scholars.index', compact('scholars', 'stats', 'scholarships', 'students', 'status', 'scholarshipId', 'search'));
    }

    /**
     * Directly enroll a student as a scholar grantee without requiring an application.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'scholarship_id' => ['required', 'exists:scholarships,id'],
            'status' => ['required', Rule::in(['active', 'for_renewal', 'completed'])],
            'approved_at' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $student = Student::with('user')->findOrFail($validated['student_id']);
        $scholarship = Scholarship::with('academicYear')->findOrFail($validated['scholarship_id']);

        // Prevent duplicate scholar entry for the same student and scholarship
        $existingScholar = Scholar::where('student_id', $student->id)
            ->where('scholarship_id', $scholarship->id)
            ->first();

        if ($existingScholar) {
            return back()->withInput()->with('error', "Student {$student->user->full_name} is already registered as a grantee for {$scholarship->name} (Status: " . ucfirst(str_replace('_', ' ', $existingScholar->status)) . ").");
        }

        $approvedAt = !empty($validated['approved_at']) ? Carbon::parse($validated['approved_at']) : now();

        $scholar = Scholar::create([
            'student_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'application_id' => null,
            'status' => $validated['status'],
            'approved_at' => $approvedAt,
        ]);

        // Audit Trail in SystemLog
        SystemLog::record(
            'Scholar',
            'direct_grantee_enrolled',
            "Directly enrolled student {$student->user->full_name} ({$student->student_number}) into scholarship '{$scholarship->name}' with status " . ucfirst(str_replace('_', ' ', $scholar->status)) . (!empty($validated['remarks']) ? ". Remarks: {$validated['remarks']}" : "."),
            $scholar,
            [
                'student_id' => $student->id,
                'scholarship_id' => $scholarship->id,
                'status' => $scholar->status,
                'approved_at' => $scholar->approved_at,
                'remarks' => $validated['remarks'] ?? null,
            ]
        );

        // Notify Student User
        if ($student->user) {
            $student->user->notify(new ScholarStatusUpdated($scholar));
        }

        return redirect()->route('admin.scholars.index')
            ->with('success', "Grantee {$student->user->full_name} was successfully enrolled into {$scholarship->name}!");
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
