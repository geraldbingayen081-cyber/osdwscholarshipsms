<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Scholarship;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_create_academic_year()
    {
        $response = $this->actingAs($this->admin)->post('/admin/academic-years', [
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/academic-years');
        $this->assertDatabaseHas('academic_years', [
            'name' => 'AY 2026-2027',
            'status' => 'active',
        ]);
    }

    public function test_activating_new_academic_year_deactivates_previous_active_year()
    {
        $ay1 = AcademicYear::create([
            'name' => 'AY 2025-2026',
            'start_date' => '2025-08-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);

        $ay2 = AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'inactive',
        ]);

        // Activate AY2
        $response = $this->actingAs($this->admin)->post("/admin/academic-years/{$ay2->id}/activate");

        $response->assertRedirect('/admin/academic-years');

        $this->assertEquals('inactive', $ay1->fresh()->status);
        $this->assertEquals('active', $ay2->fresh()->status);
    }

    public function test_admin_can_create_semester()
    {
        $ay = AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/semesters', [
            'academic_year_id' => $ay->id,
            'name' => 'First Semester',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-20',
            'status' => 'active',
        ]);

        $response->assertRedirect('/admin/semesters');
        $this->assertDatabaseHas('semesters', [
            'academic_year_id' => $ay->id,
            'name' => 'First Semester',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_scholarship_and_add_requirements()
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

        $response = $this->actingAs($this->admin)->post('/admin/scholarships', [
            'name' => 'CSU Academic Honor Scholarship',
            'provider' => 'CSU OSDW',
            'description' => 'Merit-based scholarship for top students',
            'benefits' => '100% Tuition Fee Discount',
            'available_slots' => 25,
            'application_start_date' => '2026-08-01',
            'application_deadline' => '2026-10-31',
            'coverage_type' => 'continuing',
            'academic_year_id' => $ay->id,
            'semester_id' => $sem->id,
            'status' => 'open',
        ]);

        $scholarship = Scholarship::where('name', 'CSU Academic Honor Scholarship')->first();
        $this->assertNotNull($scholarship);
        $this->assertEquals('Continuing / Multi-Year', $scholarship->coverage_type_label);
        $this->assertTrue($scholarship->isContinuing());
        $response->assertRedirect("/admin/scholarships/{$scholarship->id}");

        // Add Eligibility requirement
        $this->actingAs($this->admin)->post("/admin/scholarships/{$scholarship->id}/requirements", [
            'requirement_name' => 'GWA of 1.75 or higher',
            'requirement_type' => 'eligibility',
            'is_required' => 1,
        ]);

        // Add Document requirement
        $this->actingAs($this->admin)->post("/admin/scholarships/{$scholarship->id}/requirements", [
            'requirement_name' => 'Certificate of Grades',
            'requirement_type' => 'document',
            'is_required' => 1,
        ]);

        $this->assertDatabaseHas('scholarship_requirements', [
            'scholarship_id' => $scholarship->id,
            'requirement_name' => 'GWA of 1.75 or higher',
            'requirement_type' => 'eligibility',
        ]);

        $this->assertDatabaseHas('scholarship_requirements', [
            'scholarship_id' => $scholarship->id,
            'requirement_name' => 'Certificate of Grades',
            'requirement_type' => 'document',
        ]);
    }

    public function test_admin_can_delete_scholarship()
    {
        $scholarship = Scholarship::create([
            'name' => 'Scholarship to Delete',
            'provider' => 'CSU OSDW',
            'description' => 'Test description',
            'benefits' => 'Test benefits',
            'available_slots' => 10,
            'application_start_date' => '2026-08-01',
            'application_deadline' => '2026-10-31',
            'coverage_type' => 'annual',
            'school_year' => '2026-2027',
            'status' => 'open',
            'created_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('scholarships', ['id' => $scholarship->id]);

        $response = $this->actingAs($this->admin)->delete("/admin/scholarships/{$scholarship->id}");

        $response->assertRedirect('/admin/scholarships');
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('scholarships', ['id' => $scholarship->id]);
    }

    public function test_admin_can_create_scholarship_with_brand_new_school_year()
    {
        $response = $this->actingAs($this->admin)->post('/admin/scholarships', [
            'name' => 'LGU Tuguegarao Scholarship',
            'provider' => 'LGU Tuguegarao',
            'description' => 'Provincial educational grant',
            'benefits' => 'PHP 15,000 per year',
            'available_slots' => 50,
            'school_year' => '2027-2028',
            'coverage_type' => 'annual',
            'application_start_date' => '2027-08-01',
            'application_deadline' => '2027-10-31',
            'status' => 'open',
        ]);

        $scholarship = Scholarship::where('name', 'LGU Tuguegarao Scholarship')->first();
        $this->assertNotNull($scholarship);
        $this->assertEquals('School Year-Based / Annual', $scholarship->coverage_type_label);
        $this->assertTrue($scholarship->isAnnual());
        $response->assertRedirect("/admin/scholarships/{$scholarship->id}");
        $this->assertDatabaseHas('academic_years', ['name' => 'AY 2027-2028']);
    }
}
