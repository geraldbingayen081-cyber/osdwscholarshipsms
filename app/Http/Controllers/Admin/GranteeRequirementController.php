<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholar;
use App\Models\ScholarRenewal;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GranteeRequirementController extends Controller
{
    /**
     * Display the Grantees Requirements page with scholarship renewal metrics table.
     */
    public function index(Request $request)
    {
        $scholarships = Scholarship::with('requirements')->get()->map(function ($scholarship) {
            $scholarIds = Scholar::where('scholarship_id', $scholarship->id)
                ->whereIn('status', ['active', 'for_renewal', 'renewed'])
                ->pluck('id');

            $scholarship->total_grantees  = $scholarIds->count();

            $scholarship->total_approved  = ScholarRenewal::whereIn('scholar_id', $scholarIds)
                ->where('status', 'approved')
                ->distinct('scholar_id')->count('scholar_id');

            $scholarship->total_resubmit  = ScholarRenewal::whereIn('scholar_id', $scholarIds)
                ->whereIn('status', ['resubmit', 'deficient', 'rejected'])
                ->distinct('scholar_id')->count('scholar_id');

            $scholarship->active_requirements    = $scholarship->requirements->filter->isActive()->values();
            $scholarship->completed_requirements = $scholarship->requirements->filter->isCompleted()->values();

            return $scholarship;
        });

        $submittedRenewals = ScholarRenewal::with(['scholar.student.user', 'scholar.scholarship'])
            ->latest()
            ->get();

        return view('admin.grantees_requirements.index', compact('scholarships', 'submittedRenewals'));
    }

    /**
     * Verify or request resubmission for a grantee's submitted renewal document.
     */
    public function verifyRenewal(Request $request, ScholarRenewal $renewal)
    {
        $validated = $request->validate([
            'status'  => ['required', \Illuminate\Validation\Rule::in(['approved', 'verified', 'needs_resubmission', 'resubmit', 'rejected'])],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $statusMap = [
            'approved'           => 'verified',
            'verified'           => 'verified',
            'needs_resubmission' => 'needs_resubmission',
            'resubmit'           => 'needs_resubmission',
            'rejected'           => 'needs_resubmission',
        ];

        $targetStatus = $statusMap[$validated['status']] ?? 'verified';

        $renewal->update([
            'status'      => $targetStatus,
            'remarks'     => $validated['remarks'] ?? $renewal->remarks,
            'verified_at' => now(),
        ]);

        if ($targetStatus === 'verified') {
            $renewal->scholar->update(['status' => 'active']);
        } else {
            $renewal->scholar->update(['status' => 'for_renewal']);
        }

        $studentName = $renewal->scholar->student->user->full_name ?? 'Student';
        $msg = $targetStatus === 'verified'
            ? "Renewal document for {$studentName} verified and approved."
            : "Renewal document for {$studentName} flagged for resubmission.";

        return redirect()->route('admin.grantees-requirements.index')
            ->with('success', $msg);
    }

    /**
     * Dispatch a new renewal requirement request: store per-document records,
     * set deadline, and auto-notify all enrolled scholar grantees.
     */
    public function requestRequirements(Request $request)
    {
        $validated = $request->validate([
            'scholarship_id'   => ['required', 'exists:scholarships,id'],
            'semester'         => ['required', 'string', 'in:1st Semester,2nd Semester'],
            'school_year'      => ['required', 'string', 'regex:/^20\d{2}-20\d{2}$/'],
            'renewal_deadline' => ['required', 'date', 'after_or_equal:today'],
            'documents'        => ['required', 'array', 'min:1'],
            'documents.*'      => ['required', 'string', 'max:255'],
            'instructions'     => ['nullable', 'array'],
            'instructions.*'   => ['nullable', 'string', 'max:1000'],
        ], [
            'school_year.regex'              => 'School Year must be in YYYY-YYYY format (e.g. 2026-2027).',
            'documents.required'             => 'Please select at least one document requirement.',
            'renewal_deadline.after_or_equal'=> 'Deadline must be today or a future date.',
        ]);

        $scholarship       = Scholarship::findOrFail($validated['scholarship_id']);
        $semester          = $validated['semester'];
        $schoolYear        = $validated['school_year'];
        $deadline          = $validated['renewal_deadline'];
        $instructionsInput = $request->input('instructions', []);

        $storedItems = [];
        foreach ($validated['documents'] as $docName) {
            $docName = trim($docName);
            if (empty($docName)) continue;

            $docInstruction = isset($instructionsInput[$docName]) && !empty(trim($instructionsInput[$docName]))
                ? trim($instructionsInput[$docName])
                : null;

            ScholarshipRequirement::updateOrCreate(
                [
                    'scholarship_id'   => $scholarship->id,
                    'requirement_name' => $docName,
                    'semester'         => $semester,
                    'school_year'      => $schoolYear,
                ],
                [
                    'requirement_type' => 'document',
                    'instructions'     => $docInstruction,
                    'deadline'         => $deadline,
                    'status'           => 'active',
                    'is_required'      => true,
                ]
            );

            $storedItems[] = $docName;
        }

        if (empty($storedItems)) {
            return redirect()->back()->withErrors(['documents' => 'No valid requirements were provided.']);
        }

        $scholarship->update(['renewal_deadline' => $deadline]);

        $scholars          = Scholar::where('scholarship_id', $scholarship->id)->get();
        $formattedDeadline = \Carbon\Carbon::parse($deadline)->format('F d, Y');
        $notifiedCount     = 0;

        DB::transaction(function () use ($scholars, $scholarship, $formattedDeadline, $semester, $schoolYear, $storedItems, &$notifiedCount) {
            foreach ($scholars as $scholar) {
                $scholar->update(['status' => 'for_renewal']);

                if ($scholar->student && $scholar->student->user) {
                    $scholar->student->user->notify(new \App\Notifications\RenewalRequirementsRequested(
                        $scholarship,
                        $formattedDeadline,
                        $semester,
                        $schoolYear,
                        '',
                        $storedItems
                    ));
                    $notifiedCount++;
                }
            }
        });

        return redirect()->route('admin.grantees-requirements.index')
            ->with('success', "Requirements created successfully for {$semester} SY {$schoolYear}! Deadline: {$formattedDeadline}. {$notifiedCount} grantees notified.");
    }

    /**
     * Manually add a single renewal requirement for a scholarship program.
     */
    public function storeRequirement(Request $request)
    {
        $validated = $request->validate([
            'scholarship_id'   => ['required', 'exists:scholarships,id'],
            'requirement_name' => ['required', 'string', 'max:255'],
            'semester'         => ['nullable', 'string', 'max:50'],
            'school_year'      => ['nullable', 'string', 'regex:/^20\d{2}-20\d{2}$/'],
            'instructions'     => ['nullable', 'string', 'max:1000'],
            'deadline'         => ['nullable', 'date'],
            'status'           => ['nullable', \Illuminate\Validation\Rule::in(['active', 'completed'])],
        ], [
            'school_year.regex' => 'School Year must be in YYYY-YYYY format (e.g. 2026-2027).',
        ]);

        ScholarshipRequirement::create([
            'scholarship_id'   => $validated['scholarship_id'],
            'requirement_name' => $validated['requirement_name'],
            'requirement_type' => 'document',
            'semester'         => $validated['semester'] ?? null,
            'school_year'      => $validated['school_year'] ?? null,
            'instructions'     => $validated['instructions'] ?? null,
            'deadline'         => $validated['deadline'] ?? null,
            'status'           => $validated['status'] ?? 'active',
            'is_required'      => true,
        ]);

        return redirect()->route('admin.grantees-requirements.index')
            ->with('success', "Requirement '{$validated['requirement_name']}' added successfully.");
    }

    /**
     * Update a requirement's name, instructions, deadline, or status.
     */
    public function updateRequirement(Request $request, ScholarshipRequirement $requirement)
    {
        $validated = $request->validate([
            'requirement_name' => ['required', 'string', 'max:255'],
            'semester'         => ['nullable', 'string', 'max:50'],
            'school_year'      => ['nullable', 'string', 'regex:/^20\d{2}-20\d{2}$/'],
            'instructions'     => ['nullable', 'string', 'max:1000'],
            'deadline'         => ['nullable', 'date'],
            'status'           => ['required', \Illuminate\Validation\Rule::in(['active', 'completed'])],
        ], [
            'school_year.regex' => 'School Year must be in YYYY-YYYY format (e.g. 2026-2027).',
        ]);

        $requirement->update($validated);

        return redirect()->route('admin.grantees-requirements.index')
            ->with('success', "Requirement '{$requirement->requirement_name}' updated.");
    }

    /**
     * Quickly toggle a requirement status between Active and Completed.
     */
    public function updateStatus(Request $request, ScholarshipRequirement $requirement)
    {
        $validated = $request->validate([
            'status' => ['required', \Illuminate\Validation\Rule::in(['active', 'completed'])],
        ]);

        $requirement->update(['status' => $validated['status']]);
        $label = ucfirst($validated['status']);

        return redirect()->route('admin.grantees-requirements.index')
            ->with('success', "'{$requirement->requirement_name}' marked as {$label}.");
    }

    /**
     * Delete a configured renewal requirement.
     */
    public function destroyRequirement(ScholarshipRequirement $requirement)
    {
        $name = $requirement->requirement_name;
        $requirement->delete();

        return redirect()->route('admin.grantees-requirements.index')
            ->with('success', "Requirement '{$name}' deleted.");
    }
}
