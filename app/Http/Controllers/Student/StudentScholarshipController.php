<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentScholarshipController extends Controller
{
    /**
     * Display available scholarships for students.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $today = now()->startOfDay();

        $query = Scholarship::with(['academicYear', 'semester', 'requirements' => function ($q) {
            $q->where('status', 'active');
        }])
            ->withCount('scholars')
            ->where('status', 'open')
            ->whereDate('application_start_date', '<=', $today)
            ->whereDate('application_deadline', '>=', $today);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%");
            });
        }

        $scholarships = $query->latest()->paginate(9);

        // Fetch student's existing application scholarship IDs to indicate "Already Applied"
        $appliedScholarshipIds = [];
        if (Auth::user()->student) {
            $appliedScholarshipIds = Application::where('student_id', Auth::user()->student->id)
                ->pluck('scholarship_id')
                ->toArray();
        }

        return view('student.scholarships.index', compact('scholarships', 'search', 'appliedScholarshipIds'));
    }

    /**
     * Display scholarship program details.
     */
    public function show(Scholarship $scholarship)
    {
        $scholarship->load(['academicYear', 'semester', 'requirements' => function ($q) {
            $q->where('status', 'active');
        }])->loadCount('scholars');

        $student = Auth::user()->student;
        $existingApplication = null;

        if ($student) {
            $existingApplication = Application::where('student_id', $student->id)
                ->where('scholarship_id', $scholarship->id)
                ->first();
        }

        $isOpen = $scholarship->isOpenForApplication();

        return view('student.scholarships.show', compact('scholarship', 'existingApplication', 'isOpen'));
    }
}
