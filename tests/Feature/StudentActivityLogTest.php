<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\Semester;
use App\Models\Student;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;
    private Student $student;
    private User $otherStudentUser;
    private Student $otherStudent;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'OSDW',
            'email' => 'admin@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->studentUser = User::create([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_number' => '26-32424',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '3rd Year',
            'contact_number' => '+639171234567',
        ]);

        $this->otherStudentUser = User::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $this->otherStudent = Student::create([
            'user_id' => $this->otherStudentUser->id,
            'student_number' => '26-99999',
            'course' => 'Bachelor of Science in Agriculture (BSA)',
            'year_level' => '2nd Year',
            'contact_number' => '+639181234567',
        ]);
    }

    public function test_student_can_view_own_activity_logs_and_related_application_events_only(): void
    {
        $ay = AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);

        $sem = Semester::create([
            'academic_year_id' => $ay->id,
            'name' => 'First Semester',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-20',
            'status' => 'active',
        ]);

        $scholarship = Scholarship::create([
            'name' => 'Academic Merit Grant',
            'provider' => 'CSU OSDW',
            'description' => 'Merit scholarship',
            'benefits' => 'Full Tuition',
            'available_slots' => 20,
            'application_start_date' => now()->subDays(2)->format('Y-m-d'),
            'application_deadline' => now()->addDays(20)->format('Y-m-d'),
            'status' => 'open',
            'academic_year_id' => $ay->id,
            'semester_id' => $sem->id,
        ]);

        $application = Application::create([
            'student_id' => $this->student->id,
            'scholarship_id' => $scholarship->id,
            'student_gwa' => 1.50,
            'monthly_income' => 10000,
            'status' => 'under_review',
            'submitted_at' => now(),
        ]);

        // 1. Student's own login log
        SystemLog::record(
            'Authentication',
            'login',
            'Juan logged into the student portal.',
            $this->studentUser,
            null,
            $this->studentUser
        );

        // 2. Admin updating student's application status
        SystemLog::record(
            'Application',
            'status_change',
            'Updated Juan application #APP-' . $application->id . ' to Under Review.',
            $application,
            null,
            $this->adminUser
        );

        // 3. Another student's log (should NOT be visible to Juan)
        SystemLog::record(
            'Authentication',
            'login',
            'Maria logged into the student portal.',
            $this->otherStudentUser,
            null,
            $this->otherStudentUser
        );

        // 4. Unrelated admin system settings log (should NOT be visible to Juan)
        SystemLog::record(
            'Settings',
            'update_settings',
            'Admin updated global campus branding settings.',
            null,
            null,
            $this->adminUser
        );

        $response = $this->actingAs($this->studentUser)
            ->get(route('student.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('Activity Log & Account History');
        $response->assertSee('Juan logged into the student portal.');
        $response->assertSee('Updated Juan application #APP-' . $application->id . ' to Under Review.');
        $response->assertDontSee('Maria logged into the student portal.');
        $response->assertDontSee('Admin updated global campus branding settings.');
    }

    public function test_student_profile_update_records_activity_log(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->put(route('student.profile.update'), [
                'first_name' => 'Juanito',
                'middle_name' => 'Dela',
                'last_name' => 'Cruz',
                'email' => 'juan@csu.edu.ph',
                'student_number' => '26-32424',
                'course' => 'Bachelor of Science in Information Technology (BSIT)',
                'year_level' => '4th Year',
                'contact_number' => '9171234567',
            ]);

        $response->assertRedirect(route('student.profile.show'));

        $this->assertDatabaseHas('system_logs', [
            'user_id' => $this->studentUser->id,
            'log_type' => 'Student',
            'action' => 'update_profile',
        ]);
    }
}

