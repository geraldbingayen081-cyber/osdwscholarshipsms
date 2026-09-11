<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    /**
     * Display a listing of semesters.
     */
    public function index(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');

        $query = Semester::with('academicYear')->withCount('scholarships');

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $semesters = $query->latest('start_date')->paginate(10);
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.semesters.index', compact('semesters', 'academicYears', 'academicYearId'));
    }

    /**
     * Show the form for creating a new semester.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $semesterNames = ['First Semester', 'Second Semester', 'Summer'];

        return view('admin.semesters.create', compact('academicYears', 'semesterNames'));
    }

    /**
     * Store a newly created semester in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'end_date.after' => 'Semester end date must be after the start date.',
            'academic_year_id.required' => 'Please select an Academic Year.',
        ]);

        Semester::create($validated);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester created successfully.');
    }

    /**
     * Show the form for editing the specified semester.
     */
    public function edit(Semester $semester)
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $semesterNames = ['First Semester', 'Second Semester', 'Summer'];

        return view('admin.semesters.edit', compact('semester', 'academicYears', 'semesterNames'));
    }

    /**
     * Update the specified semester in storage.
     */
    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'end_date.after' => 'Semester end date must be after the start date.',
        ]);

        $semester->update($validated);

        return redirect()->route('admin.semesters.index')
            ->with('success', 'Semester updated successfully.');
    }
}
