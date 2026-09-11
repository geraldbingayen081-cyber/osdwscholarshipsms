<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Scholarship;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ScholarshipController extends Controller
{
    /**
     * Display a listing of scholarships.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $schoolYear = $request->get('school_year') ?? $request->get('academic_year_id');
        $status = $request->get('status');

        $query = Scholarship::with(['academicYear', 'semester'])
            ->withCount(['requirements', 'applications', 'scholars']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%");
            });
        }

        if ($schoolYear) {
            $cleanSY = str_replace('AY ', '', $schoolYear);
            $query->where(function ($q) use ($schoolYear, $cleanSY) {
                $q->where('school_year', $cleanSY)
                  ->orWhere('school_year', $schoolYear)
                  ->orWhere('academic_year_id', $schoolYear)
                  ->orWhereHas('academicYear', function ($ayq) use ($schoolYear, $cleanSY) {
                      $ayq->where('name', $schoolYear)
                          ->orWhere('name', "AY {$cleanSY}")
                          ->orWhere('name', 'like', "%{$cleanSY}%")
                          ->orWhere('id', $schoolYear);
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $scholarships = $query->latest()->paginate(10)->withQueryString();

        // Dynamically collect all available distinct school years from scholarships and academic years
        $scholarshipSYs = Scholarship::whereNotNull('school_year')->where('school_year', '!=', '')->pluck('school_year')->toArray();
        $academicYearSYs = AcademicYear::pluck('name')->map(fn($n) => trim(str_replace('AY ', '', $n)))->toArray();

        $availableSchoolYears = collect(array_merge($scholarshipSYs, $academicYearSYs))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        if ($availableSchoolYears->isEmpty()) {
            $availableSchoolYears = collect(['2026-2027']);
        }

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.scholarships.index', compact('scholarships', 'availableSchoolYears', 'academicYears', 'search', 'schoolYear', 'status'));
    }

    /**
     * Show the form for creating a new scholarship.
     */
    public function create()
    {
        return view('admin.scholarships.create');
    }

    /**
     * Store a newly created scholarship in storage.
     */
    public function store(Request $request)
    {
        if (!$request->has('school_year') && $request->has('academic_year_id')) {
            $ay = AcademicYear::find($request->input('academic_year_id'));
            if ($ay) {
                $syName = str_replace('AY ', '', $ay->name);
                $request->merge(['school_year' => $syName]);
            }
        }

        if (!$request->has('school_year')) {
            $request->merge(['school_year' => '2026-2027']);
        }

        if (!$request->has('coverage_type')) {
            $request->merge(['coverage_type' => 'continuing']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'school_year' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{4}$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('-', $value);
                    if (count($parts) === 2) {
                        $y1 = intval($parts[0]);
                        $y2 = intval($parts[1]);
                        if ($y2 !== $y1 + 1) {
                            $fail('The School Year span must be consecutive years (e.g. 2026-2027). Redundant or multi-year spans are invalid.');
                        }
                    }
                },
            ],
            'description' => ['required', 'string'],
            'benefits' => ['required', 'string'],
            'min_gwa' => ['nullable', 'numeric', 'min:1.00', 'max:5.00'],
            'max_household_income' => ['nullable', 'numeric', 'min:0'],
            'is_mutually_exclusive' => ['nullable', 'boolean'],
            'available_slots' => ['required', 'integer', 'min:1'],
            'application_start_date' => ['required', 'date'],
            'application_deadline' => ['required', 'date', 'after_or_equal:application_start_date'],
            'coverage_type' => ['required', Rule::in(['continuing', 'annual'])],
            'status' => ['required', Rule::in(['draft', 'open', 'closed', 'archived'])],
        ], [
            'school_year.regex' => 'The School Year must be in XXXX-XXXX format (e.g. 2026-2027).',
            'available_slots.min' => 'Available slots must be at least 1.',
            'application_deadline.after_or_equal' => 'Application deadline cannot be before the application start date.',
            'coverage_type.required' => 'Please select a Coverage Type for this scholarship program.',
        ]);

        // Auto sync AcademicYear for backward compatibility
        $parts = explode('-', $validated['school_year']);
        $y1 = isset($parts[0]) && is_numeric($parts[0]) ? intval($parts[0]) : 2026;
        $y2 = isset($parts[1]) && is_numeric($parts[1]) ? intval($parts[1]) : ($y1 + 1);

        $ay = AcademicYear::firstOrCreate(
            ['name' => "AY {$validated['school_year']}"],
            [
                'start_date' => "{$y1}-08-01",
                'end_date' => "{$y2}-06-30",
                'status' => 'inactive',
            ]
        );
        $validated['academic_year_id'] = $ay->id;
        $validated['created_by'] = Auth::id();
        $validated['is_mutually_exclusive'] = $request->has('is_mutually_exclusive');

        $scholarship = Scholarship::create($validated);

        return redirect()->route('admin.scholarships.show', $scholarship->id)
            ->with('success', 'Scholarship created successfully. You may now add requirements.');
    }

    /**
     * Display scholarship details and requirements management.
     */
    public function show(Scholarship $scholarship)
    {
        $scholarship->load(['academicYear', 'semester', 'creator', 'requirements', 'applications.student.user']);
        $scholarship->loadCount(['applications', 'scholars']);

        return view('admin.scholarships.show', compact('scholarship'));
    }

    /**
     * Show the form for editing the specified scholarship.
     */
    public function edit(Scholarship $scholarship)
    {
        return view('admin.scholarships.edit', compact('scholarship'));
    }

    /**
     * Update the specified scholarship in storage.
     */
    public function update(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'school_year' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{4}$/',
                function ($attribute, $value, $fail) {
                    $parts = explode('-', $value);
                    if (count($parts) === 2) {
                        $y1 = intval($parts[0]);
                        $y2 = intval($parts[1]);
                        if ($y2 !== $y1 + 1) {
                            $fail('The School Year span must be consecutive years (e.g. 2026-2027). Redundant or multi-year spans are invalid.');
                        }
                    }
                },
            ],
            'description' => ['required', 'string'],
            'benefits' => ['required', 'string'],
            'min_gwa' => ['nullable', 'numeric', 'min:1.00', 'max:5.00'],
            'max_household_income' => ['nullable', 'numeric', 'min:0'],
            'is_mutually_exclusive' => ['nullable', 'boolean'],
            'available_slots' => ['required', 'integer', 'min:1'],
            'application_start_date' => ['required', 'date'],
            'application_deadline' => ['required', 'date', 'after_or_equal:application_start_date'],
            'coverage_type' => ['required', Rule::in(['continuing', 'annual'])],
            'status' => ['required', Rule::in(['draft', 'open', 'closed', 'archived'])],
        ], [
            'school_year.regex' => 'The School Year must be in XXXX-XXXX format (e.g. 2026-2027).',
            'available_slots.min' => 'Available slots must be at least 1.',
            'application_deadline.after_or_equal' => 'Application deadline cannot be before the application start date.',
            'coverage_type.required' => 'Please select a Coverage Type for this scholarship program.',
        ]);

        $parts = explode('-', $validated['school_year']);
        $y1 = isset($parts[0]) && is_numeric($parts[0]) ? intval($parts[0]) : 2026;
        $y2 = isset($parts[1]) && is_numeric($parts[1]) ? intval($parts[1]) : ($y1 + 1);

        $ay = AcademicYear::firstOrCreate(
            ['name' => "AY {$validated['school_year']}"],
            [
                'start_date' => "{$y1}-08-01",
                'end_date' => "{$y2}-06-30",
                'status' => 'inactive',
            ]
        );
        $validated['academic_year_id'] = $ay->id;
        $validated['is_mutually_exclusive'] = $request->has('is_mutually_exclusive');

        $scholarship->update($validated);

        return redirect()->route('admin.scholarships.show', $scholarship->id)
            ->with('success', 'Scholarship details updated successfully.');
    }

    /**
     * Quickly update scholarship status (e.g. open/publish, close, archive).
     */
    public function updateStatus(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'open', 'closed', 'archived'])],
        ]);

        $scholarship->update($validated);

        return back()->with('success', "Scholarship status changed to " . ucfirst($validated['status']) . ".");
    }

    /**
     * Remove the specified scholarship from storage.
     */
    public function destroy(Scholarship $scholarship)
    {
        $scholarshipName = $scholarship->name;
        $scholarship->delete();

        return redirect()->route('admin.scholarships.index')
            ->with('success', "Scholarship \"{$scholarshipName}\" was deleted successfully.");
    }
}
