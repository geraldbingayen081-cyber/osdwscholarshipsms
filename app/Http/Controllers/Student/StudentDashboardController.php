<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\ScholarCompliance;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('login')->with('error', 'Student profile not found.');
        }

        $myApplications = Application::with('scholarship')
            ->where('student_id', $student->id)
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_applications' => Application::where('student_id', $student->id)->count(),
            'pending_applications' => Application::where('student_id', $student->id)
                ->whereIn('status', ['submitted', 'under_review', 'incomplete'])
                ->count(),
            'approved_applications' => Application::where('student_id', $student->id)
                ->where('status', 'approved')
                ->count(),
            'active_scholarships' => Scholar::where('student_id', $student->id)
                ->where('status', 'active')
                ->count(),
        ];

        $availableScholarships = Scholarship::where('status', 'open')
            ->whereDate('application_start_date', '<=', now())
            ->whereDate('application_deadline', '>=', now())
            ->latest()
            ->take(4)
            ->get();

        $myScholars = Scholar::with(['scholarship.academicYear', 'scholarship.semester', 'scholarship.requirements', 'renewals'])
            ->where('student_id', $student->id)
            ->get();

        $scholarIds = $myScholars->pluck('id');

        $myCompliances = ScholarCompliance::whereIn('scholar_id', $scholarIds)
            ->with(['complianceRequest.scholarship', 'complianceRequest.requirements', 'documents.requirement'])
            ->latest()
            ->get();

        $activeScholarRecord = $myScholars->firstWhere('status', 'active');
        $forRenewalRecord = $myScholars->firstWhere('status', 'for_renewal');
        $hasActiveScholarship = $student->hasActiveScholarship();
        $activeScholar = $student->activeScholarship();

        return view('student.dashboard', compact(
            'user', 
            'student', 
            'myApplications', 
            'stats', 
            'availableScholarships', 
            'myScholars', 
            'myCompliances',
            'activeScholarRecord', 
            'forRenewalRecord',
            'hasActiveScholarship',
            'activeScholar'
        ));
    }
}
