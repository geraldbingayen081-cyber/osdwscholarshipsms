<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ComplianceRequest;
use App\Models\ComplianceRequirement;
use App\Models\Scholar;
use App\Models\ScholarCompliance;
use App\Models\ScholarComplianceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentComplianceController extends Controller
{
    /**
     * Display listing of all compliance requests assigned to the logged-in student.
     */
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(403, 'Unauthorized. Student profile required.');
        }

        $scholarIds = Scholar::where('student_id', $student->id)->pluck('id');

        $compliances = ScholarCompliance::whereIn('scholar_id', $scholarIds)
            ->with(['complianceRequest.scholarship', 'complianceRequest.requirements', 'documents.requirement'])
            ->latest()
            ->paginate(10);

        return view('student.compliance.index', compact('compliances'));
    }

    /**
     * Display detailed compliance submission view with all requirements.
     */
    public function show(ComplianceRequest $complianceRequest)
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(403, 'Unauthorized. Student profile required.');
        }

        $scholar = Scholar::where('student_id', $student->id)
            ->where('scholarship_id', $complianceRequest->scholarship_id)
            ->first();

        if (!$scholar) {
            abort(403, 'You are not an active scholar under this scholarship program.');
        }

        $scholarCompliance = ScholarCompliance::firstOrCreate(
            [
                'compliance_request_id' => $complianceRequest->id,
                'scholar_id' => $scholar->id,
            ],
            [
                'status' => 'not_submitted',
            ]
        );

        $scholarCompliance->loadMissing(['documents.requirement', 'complianceRequest.requirements']);
        $scholarCompliance->recalculateStatus();

        return view('student.compliance.show', compact('complianceRequest', 'scholar', 'scholarCompliance'));
    }

    /**
     * Submit or resubmit an individual required document for a compliance request.
     */
    public function submitDocument(Request $request, ComplianceRequest $complianceRequest, ComplianceRequirement $requirement)
    {
        $student = Auth::user()->student;

        if (!$student) {
            abort(403, 'Unauthorized.');
        }

        $scholar = Scholar::where('student_id', $student->id)
            ->where('scholarship_id', $complianceRequest->scholarship_id)
            ->firstOrFail();

        $scholarCompliance = ScholarCompliance::where('compliance_request_id', $complianceRequest->id)
            ->where('scholar_id', $scholar->id)
            ->firstOrFail();

        $validated = $request->validate([
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'student_remarks' => ['nullable', 'string', 'max:1000'],
        ], [
            'document_file.required' => 'Please select a document file to upload.',
            'document_file.mimes' => 'Document must be a file of type: PDF, JPG, PNG, or WEBP.',
            'document_file.max' => 'Document file size must not exceed 10MB.',
        ]);

        $file = $request->file('document_file');
        $originalName = $file->getClientOriginalName();
        $storedPath = $file->store("compliance_documents/{$scholar->id}", 'public');

        // Check if a document record already exists for this requirement
        $existingDoc = ScholarComplianceDocument::where('scholar_compliance_id', $scholarCompliance->id)
            ->where('compliance_requirement_id', $requirement->id)
            ->first();

        if ($existingDoc) {
            // Delete old file if present
            if ($existingDoc->file_path && Storage::disk('public')->exists($existingDoc->file_path)) {
                Storage::disk('public')->delete($existingDoc->file_path);
            }

            $existingDoc->update([
                'file_path' => $storedPath,
                'original_filename' => $originalName,
                'student_remarks' => $validated['student_remarks'] ?? null,
                'verification_status' => 'pending',
                'admin_remarks' => null, // clear previous rejection/correction reason
                'verified_by' => null,
                'verified_at' => null,
            ]);
        } else {
            ScholarComplianceDocument::create([
                'scholar_compliance_id' => $scholarCompliance->id,
                'compliance_requirement_id' => $requirement->id,
                'file_path' => $storedPath,
                'original_filename' => $originalName,
                'student_remarks' => $validated['student_remarks'] ?? null,
                'verification_status' => 'pending',
            ]);
        }

        // Update overall scholar compliance status
        $scholarCompliance->update(['submitted_at' => now()]);
        $scholarCompliance->recalculateStatus();

        return back()->with('success', "File for '{$requirement->name}' was uploaded successfully.");
    }

    /**
     * Preview student's uploaded document inline in browser.
     */
    public function viewDocument(ScholarComplianceDocument $document)
    {
        $student = Auth::user()->student;

        if (!$student || $document->scholarCompliance->scholar->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found on storage disk.');
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
}
