<?php

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('scholarship available slots dynamically updates when applications are approved and un-approved', function () {
    $admin = User::create([
        'first_name' => 'Admin',
        'last_name' => 'Officer',
        'email' => 'admin.test@csu.edu.ph',
        'password' => bcrypt('password123'),
        'role' => 'admin',
    ]);

    $studentUser = User::create([
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'email' => 'juan@csu.edu.ph',
        'password' => bcrypt('password123'),
        'role' => 'student',
    ]);

    $student = Student::create([
        'user_id' => $studentUser->id,
        'student_number' => '26-99999',
        'course' => 'BSIT',
        'year_level' => '3rd Year',
        'contact_number' => '09123456789',
    ]);

    $academicYear = AcademicYear::create([
        'name' => 'AY 2026-2027',
        'status' => 'active',
        'start_date' => '2026-08-01',
        'end_date' => '2027-06-30',
    ]);

    $scholarship = Scholarship::create([
        'academic_year_id' => $academicYear->id,
        'school_year' => '2026-2027',
        'name' => 'Tulong Dunong Program',
        'provider' => 'CHED',
        'description' => 'Test scholarship description',
        'benefits' => 'PHP 15,000 allowance',
        'available_slots' => 50,
        'application_start_date' => now()->subDay(),
        'application_deadline' => now()->addDays(30),
        'coverage_type' => 'continuing',
        'status' => 'open',
        'created_by' => $admin->id,
    ]);

    // Initial state: 0 approved scholars, 50 remaining slots -> "50/50"
    expect($scholarship->remaining_slots)->toBe(50);
    expect($scholarship->slots_display)->toBe('50/50');

    // Create an application
    $application = Application::create([
        'student_id' => $student->id,
        'scholarship_id' => $scholarship->id,
        'status' => 'submitted',
        'submitted_at' => now(),
    ]);

    // Approving the application
    $response = $this->actingAs($admin)
        ->post(route('admin.applications.update-status', $application->id), [
            'status' => 'approved',
            'remarks' => 'Approved scholar',
        ]);

    $response->assertRedirect();
    
    // Refresh model & check dynamic remaining slots -> "49/50"
    $scholarship->refresh();
    expect($scholarship->approved_scholars_count)->toBe(1);
    expect($scholarship->remaining_slots)->toBe(49);
    expect($scholarship->slots_display)->toBe('49/50');

    // Rejecting / Un-approving returns the slot back -> "50/50"
    $this->actingAs($admin)
        ->post(route('admin.applications.update-status', $application->id), [
            'status' => 'rejected',
            'remarks' => 'Disqualified',
        ]);

    $scholarship->refresh();
    expect($scholarship->approved_scholars_count)->toBe(0);
    expect($scholarship->remaining_slots)->toBe(50);
    expect($scholarship->slots_display)->toBe('50/50');
});
