<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Scholar;
use App\Models\ScholarRenewal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ScholarRenewalController extends Controller
{
    /**
     * Store submitted renewal document for an active/for-renewal scholar.
     */
    public function store(Request $request, Scholar $scholar)
    {
        // Ensure student owns this scholar record
        $student = auth()->user()->student;
        if (!$student || $scholar->student_id !== $student->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'requirement_type' => ['required', 'string', 'max:255'],
            'renewal_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'renewal_doc'      => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'remarks'          => ['nullable', 'string', 'max:500'],
        ], [
            'requirement_type.required' => 'Please select a requirement type.',
        ]);

        $file = $request->file('renewal_document') ?? $request->file('renewal_doc');
        if (!$file) {
            return redirect()->back()->withErrors(['renewal_document' => 'Please select a document file to upload.']);
        }

        DB::transaction(function () use ($scholar, $file, $validated) {
            $path = $file->store("renewals/{$scholar->id}", 'local');

            ScholarRenewal::create([
                'scholar_id'        => $scholar->id,
                'academic_year_id'  => $scholar->scholarship->academic_year_id,
                'semester_id'       => $scholar->scholarship->semester_id,
                'requirement_type'  => $validated['requirement_type'],
                'file_path'         => $path,
                'original_filename' => $file->getClientOriginalName(),
                'status'            => 'pending',
                'remarks'           => $validated['remarks'] ?? "Submitted renewal requirement: {$validated['requirement_type']}",
                'submitted_at'      => now(),
            ]);

            // Keep status as for_renewal until Admin verifies and approves
            $scholar->update(['status' => 'for_renewal']);
        });

        return redirect()->route('student.dashboard')
            ->with('success', 'Scholarship renewal document uploaded successfully! Admin will review your submission.');
    }
}
