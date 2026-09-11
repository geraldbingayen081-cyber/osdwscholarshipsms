<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display analytical reports and statistics for scholarship programs.
     */
    public function index(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');

        $selectedAY = $academicYearId
            ? AcademicYear::find($academicYearId)
            : AcademicYear::where('status', 'active')->first();

        // Application metrics
        $appQuery = Application::query();
        if ($selectedAY) {
            $appQuery->whereHas('scholarship', function ($q) use ($selectedAY) {
                $q->where('academic_year_id', $selectedAY->id);
            });
        }

        $totalApplications = (clone $appQuery)->count();
        $approvedCount = (clone $appQuery)->where('status', 'approved')->count();
        $rejectedCount = (clone $appQuery)->where('status', 'rejected')->count();
        $pendingCount = (clone $appQuery)->whereIn('status', ['submitted', 'under_review'])->count();
        $incompleteCount = (clone $appQuery)->where('status', 'incomplete')->count();

        $acceptanceRate = $totalApplications > 0
            ? round(($approvedCount / $totalApplications) * 100, 1)
            : 0;

        // Scholarship Slot Utilization
        $scholarshipQuery = Scholarship::withCount(['applications', 'scholars']);
        if ($selectedAY) {
            $scholarshipQuery->where('academic_year_id', $selectedAY->id);
        }
        $scholarships = $scholarshipQuery->orderBy('name')->get();

        // Breakdown by Course
        $courseQuery = Student::select('course', DB::raw('count(*) as total_students'))
            ->groupBy('course');
        $courseBreakdown = $courseQuery->get();

        // Scholars breakdown by scholarship program
        $scholarsQuery = Scholar::with(['scholarship', 'student.user']);
        if ($selectedAY) {
            $scholarsQuery->whereHas('scholarship', function ($q) use ($selectedAY) {
                $q->where('academic_year_id', $selectedAY->id);
            });
        }
        $recentScholars = $scholarsQuery->latest('approved_at')->take(10)->get();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        return view('admin.reports.index', compact(
            'academicYears',
            'selectedAY',
            'totalApplications',
            'approvedCount',
            'rejectedCount',
            'pendingCount',
            'incompleteCount',
            'acceptanceRate',
            'scholarships',
            'courseBreakdown',
            'recentScholars'
        ));
    }
}
