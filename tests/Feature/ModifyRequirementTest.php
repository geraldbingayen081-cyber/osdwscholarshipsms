<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModifyRequirementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $studentUser;
    private Scholarship $scholarship;
    private ScholarshipRequirement $requirement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'first_name' => 'OSDW',
            'last_name' => 'Staff',
            'email' => 'staff@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->studentUser = User::create([
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $ay = AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'CHED UniFAST Tertiary Education Subsidy',
            'provider' => 'CHED UniFAST',
            'school_year' => '2026-2027',
            'description' => 'Tertiary education grant',
            'benefits' => 'P20,000 stipend',
            'available_slots' => 50,
            'application_start_date' => '2026-08-01',
            'application_deadline' => '2026-10-31',
            'coverage_type' => 'academic_year',
            'academic_year_id' => $ay->id,
            'status' => 'open',
            'created_by' => $this->admin->id,
        ]);

        $this->requirement = ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Enrollment (COE)',
            'instructions' => 'Submit your latest Certificate of Enrollment signed by the Registrar.',
            'requirement_type' => 'document',
            'deadline' => '2026-09-30',
            'status' => 'active',
            'is_required' => true,
        ]);
    }

    public function test_admin_can_view_modify_requirements_interface()
    {
        $response = $this->actingAs($this->admin)->get('/admin/grantees-requirements');

        $response->assertStatus(200);
        $response->assertSee('Scholarship Program Checklists');
        $response->assertSee('Certificate of Enrollment (COE)');
        $response->assertSee('Active');
    }

    public function test_admin_can_update_requirement_instructions_deadline_and_status()
    {
        $response = $this->actingAs($this->admin)->put("/admin/grantees-requirements/requirements/{$this->requirement->id}", [
            'requirement_name' => 'Photocopy of Student ID',
            'instructions' => 'Upload a clear photocopy of your valid Student ID.',
            'deadline' => '2026-09-30',
            'status' => 'active',
            'is_required' => 1,
        ]);

        $response->assertRedirect('/admin/grantees-requirements');

        $this->assertDatabaseHas('scholarship_requirements', [
            'id' => $this->requirement->id,
            'requirement_name' => 'Photocopy of Student ID',
            'instructions' => 'Upload a clear photocopy of your valid Student ID.',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_toggle_requirement_status_between_active_and_completed()
    {
        $response = $this->actingAs($this->admin)->patch("/admin/grantees-requirements/requirements/{$this->requirement->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertRedirect('/admin/grantees-requirements');
        $this->assertEquals('completed', $this->requirement->fresh()->status);

        // Reopen requirement
        $response2 = $this->actingAs($this->admin)->patch("/admin/grantees-requirements/requirements/{$this->requirement->id}/status", [
            'status' => 'active',
        ]);

        $this->assertEquals('active', $this->requirement->fresh()->status);
    }

    public function test_student_only_sees_active_requirements()
    {
        // Add a completed requirement
        ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Past Completed Requirement 2025',
            'instructions' => 'Completed requirement from previous cycle.',
            'requirement_type' => 'document',
            'status' => 'completed',
            'is_required' => true,
        ]);

        $response = $this->actingAs($this->studentUser)->get("/student/scholarships/{$this->scholarship->id}");

        $response->assertStatus(200);
        $response->assertSee('Certificate of Enrollment (COE)');
        $response->assertDontSee('Past Completed Requirement 2025');
    }
}
