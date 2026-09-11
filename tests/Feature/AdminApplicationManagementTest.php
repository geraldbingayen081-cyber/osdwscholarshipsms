<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminApplicationManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $studentUser;
    private Student $student;
    private Scholarship $scholarship;
    private Application $application;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->studentUser = User::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_number' => '26-32424',
            'course' => 'BSIT',
            'year_level' => '2nd Year',
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
            'name' => 'CSU OSDW Grant',
            'provider' => 'CSU OSDW',
            'description' => 'Financial assistance program',
            'benefits' => 'P10,000 allowance',
            'available_slots' => 10,
            'application_start_date' => '2026-08-01',
            'application_deadline' => '2026-10-31',
            'coverage_type' => 'semester',
            'academic_year_id' => $ay->id,
            'semester_id' => $sem->id,
            'status' => 'open',
            'created_by' => $this->admin->id,
        ]);

        $this->application = Application::create([
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function test_admin_can_view_applications_list()
    {
        $response = $this->actingAs($this->admin)->get('/admin/applications');

        $response->assertStatus(200);
        $response->assertSee('Maria Santos');
        $response->assertSee('CSU OSDW Grant');
    }

    public function test_admin_can_view_application_detail()
    {
        $response = $this->actingAs($this->admin)->get("/admin/applications/{$this->application->id}");

        $response->assertStatus(200);
        $response->assertSee('Maria Santos');
        $response->assertSee('26-32424');
    }

    public function test_admin_approving_application_automatically_creates_active_scholar()
    {
        $response = $this->actingAs($this->admin)->post("/admin/applications/{$this->application->id}/status", [
            'status' => 'approved',
            'remarks' => 'Congratulations! Application approved.',
        ]);

        $response->assertRedirect("/admin/applications/{$this->application->id}");
        $this->assertEquals('approved', $this->application->fresh()->status);

        // Check automated Scholar creation
        $this->assertDatabaseHas('scholars', [
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'application_id' => $this->application->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_verify_individual_application_document()
    {
        $req = ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Report Card',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        $file = UploadedFile::fake()->create('card.pdf', 200);
        $path = $file->store("documents/{$this->application->id}", 'local');

        $doc = ApplicationDocument::create([
            'application_id' => $this->application->id,
            'scholarship_requirement_id' => $req->id,
            'file_path' => $path,
            'original_filename' => 'card.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/documents/{$doc->id}/verify", [
            'status' => 'verified',
            'remarks' => 'Grade requirement verified',
        ]);

        $response->assertRedirect("/admin/applications/{$this->application->id}");
        $this->assertEquals('verified', $doc->fresh()->status);
    }

    public function test_admin_can_approve_application_via_ajax_and_receive_json()
    {
        $response = $this->actingAs($this->admin)->postJson("/admin/applications/{$this->application->id}/status", [
            'status' => 'approved',
            'remarks' => 'Approved via quick action.',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'approved',
            'status_label' => 'Approved',
        ]);
        $response->assertJsonStructure(['scholar_id', 'scholar_url', 'stats']);

        $this->assertEquals('approved', $this->application->fresh()->status);

        $this->assertDatabaseHas('scholars', [
            'student_id' => $this->student->id,
            'application_id' => $this->application->id,
            'status' => 'active',
        ]);
    }

    public function test_approved_applications_are_moved_from_default_applications_index()
    {
        // Approve application
        $this->application->update(['status' => 'approved']);
        Scholar::create([
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'application_id' => $this->application->id,
            'status' => 'active',
            'approved_at' => now(),
        ]);

        // Default applications index should exclude approved
        $indexResponse = $this->actingAs($this->admin)->get('/admin/applications');
        $indexResponse->assertStatus(200);
        $indexResponse->assertDontSee('Maria Santos');

        // Active scholars index should show the approved student
        $scholarResponse = $this->actingAs($this->admin)->get('/admin/scholars');
        $scholarResponse->assertStatus(200);
        $scholarResponse->assertSee('Maria Santos');
    }
}

