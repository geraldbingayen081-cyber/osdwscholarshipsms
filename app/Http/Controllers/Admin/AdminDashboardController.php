<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'active_scholarships' => Scholarship::where('status', 'open')->count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::whereIn('status', ['submitted', 'under_review'])->count(),
            'approved_applications' => Application::where('status', 'approved')->count(),
            'active_scholars' => Scholar::where('status', 'active')->count(),
        ];

        $recentApplications = Application::with(['student.user', 'scholarship'])
            ->latest()
            ->take(5)
            ->get();

        $scholarships = Scholarship::withCount('scholars')->latest()->take(5)->get();

        $activeAcademicYear = AcademicYear::where('status', 'active')->first();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'activeAcademicYear', 'scholarships'));
    }
}
