<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScholarManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Scholar $scholar;

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

        $studentUser = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_number' => '26-32424',
            'course' => 'BSA',
            'year_level' => '4th Year',
            'contact_number' => '+639171234567',
        ]);

        $ay = AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);

        $scholarship = Scholarship::create([
            'name' => 'Lgu Honor Scholarship',
            'provider' => 'LGU Lal-lo',
            'description' => 'Local government grant',
            'benefits' => 'P5,000 stipend',
            'available_slots' => 20,
            'application_start_date' => '2026-08-01',
            'application_deadline' => '2026-10-31',
            'coverage_type' => 'academic_year',
            'academic_year_id' => $ay->id,
            'status' => 'open',
            'created_by' => $this->admin->id,
        ]);

        $app = Application::create([
            'student_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'status' => 'approved',
            'submitted_at' => now(),
        ]);

        $this->scholar = Scholar::create([
            'student_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'application_id' => $app->id,
            'status' => 'active',
            'approved_at' => now(),
        ]);
    }

    public function test_admin_can_view_scholars_list()
    {
        $response = $this->actingAs($this->admin)->get('/admin/scholars');

        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee('Lgu Honor Scholarship');
    }

    public function test_admin_can_update_scholar_standing_status()
    {
        $response = $this->actingAs($this->admin)->post("/admin/scholars/{$this->scholar->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertRedirect("/admin/scholars/{$this->scholar->id}");
        $this->assertEquals('completed', $this->scholar->fresh()->status);
    }

    public function test_admin_can_view_scholar_profile_with_submitted_documents()
    {
        $response = $this->actingAs($this->admin)->get("/admin/scholars/{$this->scholar->id}");

        $response->assertStatus(200);
        $response->assertSee('Scholar Profile: John Doe');
        $response->assertSee('Submitted Requirements');
    }
}



