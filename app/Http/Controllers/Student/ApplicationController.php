<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    /**
     * Display student's applications.
     */
    public function index()
    {
        $student = Auth::user()->student;

        $applications = Application::with(['scholarship.academicYear', 'documents.requirement'])
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(10);

        return view('student.applications.index', compact('applications'));
    }

    /**
     * Show multi-step application wizard for a scholarship.
     */
    public function create(Scholarship $scholarship)
    {
        $student = Auth::user()->student;

        // 1. Duplicate application check
        $exists = Application::where('student_id', $student->id)
            ->where('scholarship_id', $scholarship->id)
            ->exists();

        if ($exists) {
            return redirect()->route('student.scholarships.show', $scholarship->id)
                ->with('error', 'You have already submitted an application for this scholarship program.');
        }

        // 2. Open period check
        if (!$scholarship->isOpenForApplication()) {
            return redirect()->route('student.scholarships.show', $scholarship->id)
                ->with('error', 'This scholarship program is currently not accepting applications or has passed its deadline.');
        }

        // 3. Pre-qualification rules check
        if (!is_null($scholarship->min_gwa) && $student->current_gwa && $student->current_gwa > $scholarship->min_gwa) {
            return redirect()->route('student.scholarships.show', $scholarship->id)
                ->with('error', "Your current GWA ({$student->current_gwa}) exceeds the maximum allowed cutoff ({$scholarship->min_gwa}) for this scholarship.");
        }

        $scholarship->load(['requirements' => function ($q) {
            $q->where('status', 'active');
        }]);

        return view('student.applications.create', compact('scholarship', 'student'));
    }

    /**
     * Store application submission.
     */
    public function store(Request $request, Scholarship $scholarship)
    {
        $student = Auth::user()->student;

        // Duplicate check
        $exists = Application::where('student_id', $student->id)
            ->where('scholarship_id', $scholarship->id)
            ->exists();

        if ($exists) {
            return redirect()->route('student.applications.index')
                ->with('error', 'You have already submitted an application for this scholarship.');
        }

        if (!$scholarship->isOpenForApplication()) {
            return redirect()->route('student.scholarships.show', $scholarship->id)
                ->with('error', 'Application submission period is closed.');
        }

        // Validate Document Checklist
        $documentRequirements = $scholarship->requirements()->where('status', 'active')->get();
        if ($documentRequirements->isEmpty()) {
            $documentRequirements = $scholarship->requirements;
        }

        $rules = [];
        $messages = [];

        foreach ($documentRequirements as $docReq) {
            $fieldName = 'doc_' . $docReq->id;
            if ($docReq->is_required) {
                $rules[$fieldName] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
                $messages["{$fieldName}.required"] = "Please upload required document: {$docReq->requirement_name}";
            } else {
                $rules[$fieldName] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
            }
        }

        $validated = $request->validate($rules, $messages);

        DB::transaction(function () use ($student, $scholarship, $documentRequirements, $request) {
            // Create Application Record
            $application = Application::create([
                'student_id' => $student->id,
                'scholarship_id' => $scholarship->id,
                'student_gwa' => $student->current_gwa ?? 1.75,
                'monthly_income' => $student->monthly_household_income ?? 0,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            // Save document files securely in private storage
            foreach ($documentRequirements as $docReq) {
                $fieldName = 'doc_' . $docReq->id;
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $originalName = $file->getClientOriginalName();
                    $path = $file->store("private/documents/{$application->id}", 'local');

                    ApplicationDocument::create([
                        'application_id' => $application->id,
                        'scholarship_requirement_id' => $docReq->id,
                        'doc_type' => $docReq->requirement_name,
                        'file_path' => $path,
                        'original_filename' => $originalName,
                        'status' => 'pending',
                        'verification_status' => 'pending',
                    ]);
                }
            }
        });

        return redirect()->route('student.applications.index')
            ->with('success', 'Your scholarship application and verified documents have been submitted successfully to CSU Lal-lo OSDW!');
    }

    /**
     * Display a specific application details.
     */
    public function show(Application $application)
    {
        $student = Auth::user()->student;

        if ($application->student_id !== $student->id) {
            abort(403, 'Unauthorized access to application record.');
        }

        $application->load(['scholarship.requirements', 'documents.requirement']);

        return view('student.applications.show', compact('application'));
    }

    /**
     * Deficient Document Resolution View (Deep link: /applications/{application}/resolve)
     */
    public function resolveDeficiency(Application $application)
    {
        $student = Auth::user()->student;

        if ($application->student_id !== $student->id) {
            abort(403, 'Unauthorized access.');
        }

        $application->load(['scholarship', 'documents' => function ($q) {
            $q->whereIn('status', ['needs_resubmission', 'rejected', 'deficient'])
              ->orWhere('verification_status', 'rejected');
        }]);

        return view('student.resolve-deficiency', compact('application'));
    }

    /**
     * Process Deficient Document Replacement Upload
     */
    public function updateDeficiency(Request $request, Application $application)
    {
        $student = Auth::user()->student;

        if ($application->student_id !== $student->id) {
            abort(403, 'Unauthorized access.');
        }

        $deficientDocs = $application->documents()
            ->where(fn ($q) => $q->whereIn('status', ['needs_resubmission', 'rejected', 'deficient'])->orWhere('verification_status', 'rejected'))
            ->get();

        $rules = [];
        $messages = [];

        foreach ($deficientDocs as $doc) {
            $fieldName = 'doc_replace_' . $doc->id;
            $rules[$fieldName] = ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];
            $messages["{$fieldName}.required"] = "Please upload replacement file for {$doc->doc_type}";
        }

        $request->validate($rules, $messages);

        foreach ($deficientDocs as $doc) {
            $fieldName = 'doc_replace_' . $doc->id;
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $originalName = $file->getClientOriginalName();
                $path = $file->store("private/documents/{$application->id}", 'local');

                $doc->update([
                    'file_path' => $path,
                    'original_filename' => $originalName,
                    'status' => 'pending',
                    'verification_status' => 'pending',
                    'remarks' => null,
                    'verified_at' => null,
                ]);
            }
        }

        // Return application status to under_review
        $application->update(['status' => 'under_review']);

        return redirect()->route('student.applications.show', $application->id)
            ->with('success', 'Deficient documents successfully replaced and resubmitted! Application is now under OSDW review.');
    }

    /**
     * Secure private document viewer streaming route
     */
    public function viewSecureDocument(ApplicationDocument $document)
    {
        $user = Auth::user();

        // Check ownership or staff access
        $isOwner = $user->student && $user->student->id === $document->application->student_id;
        $isStaff = $user->isAdminOrStaff();

        if (!$isOwner && !$isStaff) {
            abort(403, 'Unauthorized access to document.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found on storage server.');
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
