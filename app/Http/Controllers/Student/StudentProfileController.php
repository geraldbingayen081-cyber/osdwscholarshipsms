<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentProfileController extends Controller
{
    /**
     * Display student profile.
     */
    public function show()
    {
        $user = Auth::user();
        $student = $user->student;

        $courses = [
            'Bachelor of Science in Information Technology (BSIT)',
            'Bachelor of Science in Agriculture (BSA)',
            'Bachelor of Science in Hospitality Management (BSHM)',
            'Bachelor of Elementary Education (BEEd)',
            'Bachelor of Secondary Education (BSEd)',
        ];

        $yearLevels = ['1st Year', '2nd Year', '3rd Year', '4th Year'];

        return view('student.profile', compact('user', 'student', 'courses', 'yearLevels'));
    }

    /**
     * Update student profile & avatar photo.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:20', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'middle_name' => ['nullable', 'string', 'max:20', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'last_name' => ['required', 'string', 'max:20', 'regex:/^[a-zA-Z\s\-\'\.]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'student_number' => ['required', 'string', 'max:20', 'regex:/^\d{2}-\d{5}$/', Rule::unique('students')->ignore($student->id)],
            'course' => ['required', 'string', 'max:255'],
            'year_level' => ['required', Rule::in(['1st Year', '2nd Year', '3rd Year', '4th Year'])],
            'contact_number' => ['required', 'string', 'regex:/^(\+639\d{9}|9\d{9})$/'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'first_name.max' => 'First name must not exceed 20 characters.',
            'middle_name.max' => 'Middle name must not exceed 20 characters.',
            'last_name.max' => 'Last name must not exceed 20 characters.',
            'student_number.regex' => 'Student ID format must be XX-XXXXX (e.g. 26-32424).',
            'contact_number.regex' => 'Contact number must be exactly 10 digits starting with 9 (e.g. 9123456789).',
            'profile_photo.image' => 'Profile picture must be an image file (JPG, JPEG, PNG, or WEBP).',
            'profile_photo.max' => 'Profile picture size must not exceed 5MB.',
        ]);

        // Format contact number
        $rawContact = trim($validated['contact_number']);
        if (str_starts_with($rawContact, '9')) {
            $rawContact = '+63' . $rawContact;
        }

        DB::transaction(function () use ($user, $student, $validated, $rawContact, $request) {
            $userData = [
                'first_name' => trim($validated['first_name']),
                'middle_name' => $validated['middle_name'] ? trim($validated['middle_name']) : null,
                'last_name' => trim($validated['last_name']),
                'email' => strtolower(trim($validated['email'])),
            ];

            // Handle Profile Photo Upload
            if ($request->hasFile('profile_photo')) {
                // Delete existing photo if present
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                $photoPath = $request->file('profile_photo')->store('profile-photos', 'public');
                $userData['profile_photo_path'] = $photoPath;
            }

            $user->update($userData);

            $student->update([
                'student_number' => trim($validated['student_number']),
                'course' => $validated['course'],
                'year_level' => $validated['year_level'],
                'contact_number' => $rawContact,
            ]);
        });

        return redirect()->route('student.profile.show')
            ->with('success', 'Your student profile and photo have been updated successfully.');
    }
}
