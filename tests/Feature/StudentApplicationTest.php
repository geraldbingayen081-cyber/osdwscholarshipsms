<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentApplicationTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;
    private Student $student;
    private Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->studentUser = User::create([
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
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

        $this->scholarship = Scholarship::create([
            'name' => 'CSU Academic Honor Scholarship',
            'provider' => 'CSU OSDW',
            'description' => 'Merit-based scholarship',
            'benefits' => '100% Tuition Discount',
            'available_slots' => 30,
            'application_start_date' => now()->subDays(5)->format('Y-m-d'),
            'application_deadline' => now()->addDays(30)->format('Y-m-d'),
            'coverage_type' => 'semester',
            'academic_year_id' => $ay->id,
            'semester_id' => $sem->id,
            'status' => 'open',
            'created_by' => $admin->id,
        ]);
    }

    public function test_student_can_view_profile()
    {
        $response = $this->actingAs($this->studentUser)->get('/student/profile');

        $response->assertStatus(200);
        $response->assertSee('Juan');
        $response->assertSee('26-32424');
    }

    public function test_student_can_browse_available_scholarships()
    {
        $response = $this->actingAs($this->studentUser)->get('/student/scholarships');

        $response->assertStatus(200);
        $response->assertSee('CSU Academic Honor Scholarship');
    }

    public function test_student_can_submit_scholarship_application_with_documents()
    {
        $eligReq = ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Minimum GWA of 1.75 or better',
            'requirement_type' => 'eligibility',
            'is_required' => true,
        ]);

        $docReq = ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Registration',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        $file = UploadedFile::fake()->create('cor.pdf', 500, 'application/pdf');

        // Only upload file for docReq, not eligReq
        $response = $this->actingAs($this->studentUser)->post("/student/scholarships/{$this->scholarship->id}/apply", [
            'doc_' . $docReq->id => $file,
        ]);

        $response->assertRedirect('/student/applications');
        $this->assertDatabaseHas('applications', [
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'submitted',
        ]);
    }

    public function test_duplicate_application_submission_is_prevented()
    {
        // First application
        Application::create([
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // Attempting to apply again
        $response = $this->actingAs($this->studentUser)->post("/student/scholarships/{$this->scholarship->id}/apply", []);

        $response->assertRedirect('/student/applications');
        $response->assertSessionHas('error', 'You have already submitted an application for this scholarship.');
    }

    public function test_student_can_upload_profile_photo_and_update_profile()
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg', 600, 600)->size(4500); // 4.5MB (Allowed under 5MB)

        $response = $this->actingAs($this->studentUser)->put('/student/profile', [
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'email' => 'juan@csu.edu.ph',
            'student_number' => '26-32424',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '3rd Year',
            'contact_number' => '9171234567',
            'profile_photo' => $avatar,
        ]);

        $response->assertRedirect('/student/profile');
        $this->assertNotNull($this->studentUser->fresh()->profile_photo_path);
        Storage::disk('public')->assertExists($this->studentUser->fresh()->profile_photo_path);
    }

    public function test_student_cannot_upload_profile_photo_exceeding_5mb()
    {
        Storage::fake('public');

        $oversizedAvatar = UploadedFile::fake()->image('large_avatar.jpg')->size(6000); // 6MB (> 5MB)

        $response = $this->actingAs($this->studentUser)->put('/student/profile', [
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'email' => 'juan@csu.edu.ph',
            'student_number' => '26-32424',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '3rd Year',
            'contact_number' => '9171234567',
            'profile_photo' => $oversizedAvatar,
        ]);

        $response->assertSessionHasErrors(['profile_photo']);
    }
}
