<?php

namespace App\Http\Controllers\Admin;

use App\Enums\College;
use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
        $colleges = College::cases();

        return view('admin.students.show', compact('student', 'colleges'));
    }

    /**
     * Update the specified student's personal information and password (without requiring current password).
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($student->user_id),
            ],
            'student_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'student_number')->ignore($student->id),
            ],
            'college' => ['nullable', 'string'],
            'program' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'current_gwa' => ['nullable', 'numeric', 'between:1.00,5.00'],
            'monthly_household_income' => ['nullable', 'numeric', 'min:0'],
            'is_4ps' => ['nullable', 'boolean'],
            'municipality' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters.',
        ]);

        DB::transaction(function () use ($student, $validated, $request) {
            $user = $student->user;
            $user->first_name = $validated['first_name'];
            $user->middle_name = $validated['middle_name'] ?? null;
            $user->last_name = $validated['last_name'];
            $user->email = $validated['email'];

            // Update password directly without requiring old/current password
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $program = $validated['program'] ?? $validated['course'] ?? $student->course;

            $student->update([
                'student_number' => $validated['student_number'],
                'college' => !empty($validated['college']) ? $validated['college'] : $student->college,
                'program' => $program,
                'course' => $program,
                'year_level' => $validated['year_level'] ?? $student->year_level,
                'contact_number' => $validated['contact_number'] ?? $student->contact_number,
                'current_gwa' => $validated['current_gwa'] !== '' ? $validated['current_gwa'] : $student->current_gwa,
                'monthly_household_income' => $validated['monthly_household_income'] !== '' ? $validated['monthly_household_income'] : $student->monthly_household_income,
                'is_4ps' => $request->boolean('is_4ps'),
                'municipality' => $validated['municipality'] ?? $student->municipality,
                'barangay' => $validated['barangay'] ?? $student->barangay,
            ]);
        });

        return redirect()->route('admin.students.show', $student->id)
            ->with('success', "Student profile for {$student->user->fresh()->full_name} has been updated successfully.");
    }
}
