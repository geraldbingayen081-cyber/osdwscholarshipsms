<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScholarshipRequirementController extends Controller
{
    /**
     * Store a new requirement for a scholarship.
     */
    public function store(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'requirement_type' => ['required', Rule::in(['eligibility', 'document'])],
            'preset_name' => ['nullable', 'string', 'max:255'],
            'custom_name' => ['nullable', 'string', 'max:255'],
            'requirement_name' => ['nullable', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $finalName = $validated['custom_name'] 
            ?? $validated['preset_name'] 
            ?? $validated['requirement_name'] 
            ?? 'Requirement';

        if (empty($finalName) || $finalName === 'custom') {
            return back()->withErrors(['custom_name' => 'Please provide a specific requirement description or document title.']);
        }

        $scholarship->requirements()->create([
            'requirement_name' => $finalName,
            'requirement_type' => $validated['requirement_type'],
            'is_required' => $request->has('is_required') ? (bool)$request->input('is_required') : true,
        ]);

        return redirect()->route('admin.scholarships.show', $scholarship->id)
            ->with('success', 'Requirement added successfully.');
    }

    /**
     * Update an existing requirement.
     */
    public function update(Request $request, ScholarshipRequirement $requirement)
    {
        $validated = $request->validate([
            'requirement_name' => ['required', 'string', 'max:255'],
            'requirement_type' => ['required', Rule::in(['eligibility', 'document'])],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $requirement->update([
            'requirement_name' => $validated['requirement_name'],
            'requirement_type' => $validated['requirement_type'],
            'is_required' => $request->has('is_required') ? (bool)$validated['is_required'] : true,
        ]);

        return redirect()->route('admin.scholarships.show', $requirement->scholarship_id)
            ->with('success', 'Requirement updated successfully.');
    }

    /**
     * Remove a requirement.
     */
    public function destroy(ScholarshipRequirement $requirement)
    {
        $scholarshipId = $requirement->scholarship_id;
        $requirement->delete();

        return redirect()->route('admin.scholarships.show', $scholarshipId)
            ->with('success', 'Requirement removed successfully.');
    }
}
