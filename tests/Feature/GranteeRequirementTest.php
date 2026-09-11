<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GranteeRequirementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Coordinator',
            'email' => 'admin@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $ay = AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'CHED Tertiary Education Subsidy',
            'provider' => 'CHED UniFAST',
            'school_year' => '2026-2027',
            'description' => 'Tertiary education subsidy program',
            'benefits' => 'P20,000 per semester',
            'available_slots' => 50,
            'application_start_date' => '2026-08-01',
            'application_deadline' => '2026-10-31',
            'coverage_type' => 'academic_year',
            'academic_year_id' => $ay->id,
            'status' => 'open',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_grantees_requirements_page_and_summary_table()
    {
        $response = $this->actingAs($this->admin)->get('/admin/grantees-requirements');

        $response->assertStatus(200);
        $response->assertSee('Scholarship Program Checklists');
        $response->assertSee('Scholarship Program');
        $response->assertSee('CHED Tertiary Education Subsidy');
        $response->assertSee('Total Grantees');
        $response->assertSee('Approved Renewals');
        $response->assertSee('Needs Resubmission');
    }

    public function test_admin_can_add_renewal_requirement_for_scholarship_program()
    {
        $response = $this->actingAs($this->admin)->post('/admin/grantees-requirements/requirements', [
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Enrollment (COE)',
            'requirement_type' => 'document',
            'is_required' => 1,
        ]);

        $response->assertRedirect('/admin/grantees-requirements');
        
        $this->assertDatabaseHas('scholarship_requirements', [
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Enrollment (COE)',
            'requirement_type' => 'document',
        ]);
    }

    public function test_admin_can_delete_configured_requirement()
    {
        $req = ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Grades (COG)',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/grantees-requirements/requirements/{$req->id}");

        $response->assertRedirect('/admin/grantees-requirements');

        $this->assertDatabaseMissing('scholarship_requirements', [
            'id' => $req->id,
        ]);
    }

    public function test_admin_can_request_requirements_from_grantees_with_deadline_and_notifications()
    {
        $studentUser = User::create([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_number' => '26-99999',
            'course' => 'BSIT',
            'year_level' => '3rd Year',
            'contact_number' => '+639171112233',
        ]);

        $app = \App\Models\Application::create([
            'student_id' => $student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'approved',
            'submitted_at' => now(),
        ]);

        $scholar = Scholar::create([
            'student_id' => $student->id,
            'scholarship_id' => $this->scholarship->id,
            'application_id' => $app->id,
            'status' => 'active',
        ]);

        $futureDeadline = date('Y-m-d', strtotime('+14 days'));

        $response = $this->actingAs($this->admin)->post('/admin/grantees-requirements/request', [
            'scholarship_id' => $this->scholarship->id,
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'renewal_deadline' => $futureDeadline,
            'documents' => [
                'Certificate of Enrollment (COE)',
                'Certificate of Grades (COG)',
            ],
            'instructions' => [
                'Certificate of Enrollment (COE)' => 'Must be signed by the Registrar.',
            ],
        ]);

        $response->assertRedirect('/admin/grantees-requirements');

        // Verify scholarship renewal deadline updated
        $this->assertEquals($futureDeadline, $this->scholarship->fresh()->renewal_deadline->format('Y-m-d'));

        // Verify scholar status updated to for_renewal
        $this->assertEquals('for_renewal', $scholar->fresh()->status);

        $this->assertDatabaseHas('scholarship_requirements', [
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Enrollment (COE)',
            'instructions' => 'Must be signed by the Registrar.',
            'semester' => '1st Semester',
            'school_year' => '2026-2027',
        ]);

        // Verify notification created for student
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $studentUser->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_admin_can_verify_submitted_renewal_document_directly()
    {
        $studentUser = User::create([
            'first_name' => 'Ana',
            'last_name' => 'Reyes',
            'email' => 'ana@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_number' => '26-88888',
            'course' => 'BSIT',
            'year_level' => '2nd Year',
            'contact_number' => '+639178888888',
        ]);

        $app = \App\Models\Application::create([
            'student_id' => $student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'approved',
        ]);

        $scholar = Scholar::create([
            'student_id' => $student->id,
            'scholarship_id' => $this->scholarship->id,
            'application_id' => $app->id,
            'status' => 'for_renewal',
        ]);

        $renewal = \App\Models\ScholarRenewal::create([
            'scholar_id' => $scholar->id,
            'requirement_type' => 'Certificate of Enrollment (COE)',
            'file_path' => 'renewals/sample.pdf',
            'original_filename' => 'sample.pdf',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/grantees-requirements/renewals/{$renewal->id}/verify", [
            'status' => 'verified',
        ]);

        $response->assertRedirect('/admin/grantees-requirements');
        $this->assertEquals('verified', $renewal->fresh()->status);
        $this->assertEquals('active', $scholar->fresh()->status);
    }
}
