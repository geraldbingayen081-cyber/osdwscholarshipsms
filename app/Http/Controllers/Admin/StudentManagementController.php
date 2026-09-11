<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentManagementController extends Controller
{
    /**
     * Display a listing of registered students.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $course = $request->get('course');

        $query = Student::with(['user', 'applications', 'scholars.scholarship']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('course', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$search}%");
                  });
            });
        }

        if ($course) {
            $query->where('course', $course);
        }

        $students = $query->latest()->paginate(10);
        $courses = Student::select('course')->distinct()->pluck('course');

        return view('admin.students.index', compact('students', 'courses', 'search', 'course'));
    }

    /**
     * Display the specified student's detailed profile and history.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'applications.scholarship.academicYear', 'scholars.scholarship']);

        return view('admin.students.show', compact('student'));
    }
}
