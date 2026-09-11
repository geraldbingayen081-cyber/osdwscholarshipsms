<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Display student registration form.
     */
    public function create()
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('student.dashboard');
        }

        $courses = [
            'Bachelor of Science in Information Technology (BSIT)',
            'Bachelor of Science in Agriculture (BSA)',
            'Bachelor of Science in Hospitality Management (BSHM)',
            'Bachelor of Elementary Education (BEEd)',
            'Bachelor of Secondary Education (BSEd)',
        ];

        $yearLevels = [
            '1st Year',
            '2nd Year',
            '3rd Year',
            '4th Year',
        ];

        return view('auth.register', compact('courses', 'yearLevels'));
    }

    /**
     * Handle student registration.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:20', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'middle_name' => ['nullable', 'string', 'max:20', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'last_name' => ['required', 'string', 'max:20', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'student_number' => ['required', 'string', 'max:20', 'regex:/^\d{2}-\d{5}$/', 'unique:students,student_number'],
            'course' => ['required', 'string', 'max:255'],
            'year_level' => ['required', Rule::in(['1st Year', '2nd Year', '3rd Year', '4th Year'])],
            'contact_number' => [
                'required',
                'string',
                'regex:/^9\d{9}$/',
            ],
        ], [
            'first_name.max' => 'First name must not exceed 20 characters.',
            'middle_name.max' => 'Middle name must not exceed 20 characters.',
            'last_name.max' => 'Last name must not exceed 20 characters.',
            'first_name.regex' => 'First name must contain letters, spaces, hyphens, or apostrophes only.',
            'middle_name.regex' => 'Middle name must contain letters, spaces, hyphens, or apostrophes only.',
            'last_name.regex' => 'Last name must contain letters, spaces, hyphens, or apostrophes only.',
            'student_number.regex' => 'Student ID format must be XX-XXXXX (e.g. 26-32424).',
            'contact_number.regex' => 'Contact number must be exactly 10 digits starting with 9 (e.g. 9123456789).',
            'student_number.unique' => 'This Student ID is already registered in the system.',
            'email.unique' => 'This email address is already registered in the system.',
        ]);

        // Normalize contact number to +63 format
        $normalizedContact = '+63' . trim($validated['contact_number']);

        $user = DB::transaction(function () use ($validated, $normalizedContact) {
            $user = User::create([
                'first_name' => trim($validated['first_name']),
                'middle_name' => $validated['middle_name'] ? trim($validated['middle_name']) : null,
                'last_name' => trim($validated['last_name']),
                'email' => strtolower(trim($validated['email'])),
                'password' => Hash::make($validated['password']),
                'role' => 'student',
            ]);

            Student::create([
                'user_id' => $user->id,
                'student_number' => trim($validated['student_number']),
                'course' => $validated['course'],
                'year_level' => $validated['year_level'],
                'contact_number' => $normalizedContact,
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', 'Welcome to CSU–Lal-lo Scholarship Management System! Your account has been registered successfully.');
    }
}
