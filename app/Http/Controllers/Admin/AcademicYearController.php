<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years.
     */
    public function index()
    {
        $academicYears = AcademicYear::withCount(['semesters', 'scholarships'])
            ->orderByRaw("FIELD(status, 'active', 'inactive')")
            ->latest('start_date')
            ->paginate(10);

        return view('admin.academic_years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new academic year.
     */
    public function create()
    {
        return view('admin.academic_years.create');
    }

    /**
     * Store a newly created academic year in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'end_date.after' => 'End date must be a date after the start date.',
            'name.unique' => 'An Academic Year with this name already exists.',
        ]);

        DB::transaction(function () use ($validated) {
            // Business Rule: If new academic year is set to active, deactivate all other academic years
            if ($validated['status'] === 'active') {
                AcademicYear::query()->update(['status' => 'inactive']);
            }

            AcademicYear::create($validated);
        });

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic Year created successfully.');
    }

    /**
     * Show the form for editing the specified academic year.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    /**
     * Update the specified academic year in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('academic_years')->ignore($academicYear->id)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'end_date.after' => 'End date must be a date after the start date.',
            'name.unique' => 'An Academic Year with this name already exists.',
        ]);

        DB::transaction(function () use ($academicYear, $validated) {
            // Business Rule: If updating status to active, deactivate all other academic years
            if ($validated['status'] === 'active' && $academicYear->status !== 'active') {
                AcademicYear::where('id', '!=', $academicYear->id)->update(['status' => 'inactive']);
            }

            $academicYear->update($validated);
        });

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Academic Year updated successfully.');
    }

    /**
     * Activate the specified academic year.
     */
    public function activate(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['status' => 'inactive']);
            $academicYear->update(['status' => 'active']);
        });

        return redirect()->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$academicYear->name}' is now active.");
    }
}
