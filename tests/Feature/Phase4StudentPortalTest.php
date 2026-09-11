<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase4StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;
    private Student $student;
    private Scholarship $scholarship;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->studentUser = User::create([
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Reyes',
            'email' => 'maria@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_number' => '2026-CSU-001',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '3rd Year',
            'gwa' => 1.50,
            'monthly_income' => 15000,
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
            'name' => 'LGU Lal-lo Tertiary Education Assistance',
            'provider' => 'LGU Lal-lo',
            'description' => 'Financial assistance for deserving Lal-lo students',
            'benefits' => '₱5,000 per semester',
            'available_slots' => 50,
            'min_gwa' => 2.00,
            'max_household_income' => 25000.00,
            'application_start_date' => now()->subDays(5)->format('Y-m-d'),
            'application_deadline' => now()->addDays(30)->format('Y-m-d'),
            'coverage_type' => 'semester',
            'academic_year_id' => $ay->id,
            'semester_id' => $sem->id,
            'status' => 'open',
            'created_by' => $admin->id,
        ]);
    }

    public function test_student_dashboard_displays_open_scholarships_and_qualification_flags()
    {
        $response = $this->actingAs($this->studentUser)->get('/student/dashboard');

        $response->assertStatus(200);
        $response->assertSee('LGU Lal-lo Tertiary Education Assistance');
    }

    public function test_student_can_complete_multi_step_wizard_application()
    {
        $req = ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Registration',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        $file = UploadedFile::fake()->create('cor.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->studentUser)->post("/student/scholarships/{$this->scholarship->id}/apply", [
            'scholarship_id' => $this->scholarship->id,
            'college' => 'CICS',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '3rd Year',
            'gwa' => '1.50',
            'monthly_income' => '15000',
            'is_4ps' => '0',
            'doc_' . $req->id => $file,
            'undertaking' => '1',
        ]);

        $response->assertRedirect('/student/applications');
        $this->assertDatabaseHas('applications', [
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'submitted',
        ]);
    }

    public function test_student_can_resolve_deficient_document()
    {
        $req = ScholarshipRequirement::create([
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Grades (COG)',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        $app = Application::create([
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'deficient',
            'submitted_at' => now(),
        ]);

        $doc = ApplicationDocument::create([
            'application_id' => $app->id,
            'scholarship_requirement_id' => $req->id,
            'original_filename' => 'old_cog.pdf',
            'doc_type' => 'Certificate of Grades (COG)',
            'file_path' => 'documents/old_cog.pdf',
            'status' => 'deficient',
            'remarks' => 'Blurry copy of COG; Registrar seal missing',
        ]);

        $resolvePage = $this->actingAs($this->studentUser)->get("/student/applications/{$app->id}/resolve");
        $resolvePage->assertStatus(200);
        $resolvePage->assertSee('Blurry copy of COG; Registrar seal missing');

        $newFile = UploadedFile::fake()->create('new_cog.pdf', 600, 'application/pdf');

        $submitResolve = $this->actingAs($this->studentUser)->post("/student/applications/{$app->id}/resolve", [
            'doc_replace_' . $doc->id => $newFile,
        ]);

        $submitResolve->assertRedirect("/student/applications/{$app->id}");
        
        $this->assertDatabaseHas('application_documents', [
            'id' => $doc->id,
            'status' => 'pending',
            'remarks' => null,
        ]);

        $this->assertDatabaseHas('applications', [
            'id' => $app->id,
            'status' => 'under_review',
        ]);
    }

    public function test_admin_can_create_scholarship_with_direct_school_year_format()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/scholarships', [
            'name' => 'Lal-lo Excellence Grant 2026',
            'provider' => 'LGU Lal-lo',
            'school_year' => '2026-2027',
            'coverage_type' => 'continuing',
            'description' => 'Test description',
            'benefits' => '100% Tuition Discount',
            'available_slots' => 10,
            'application_start_date' => now()->format('Y-m-d'),
            'application_deadline' => now()->addDays(20)->format('Y-m-d'),
            'status' => 'open',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('scholarships', [
            'name' => 'Lal-lo Excellence Grant 2026',
            'school_year' => '2026-2027',
        ]);
    }

    public function test_admin_cannot_create_scholarship_with_redundant_or_invalid_school_year_format()
    {
        $admin = User::where('role', 'admin')->first();

        // Testing redundant span (2026-2026)
        $response1 = $this->actingAs($admin)->post('/admin/scholarships', [
            'name' => 'Invalid Grant 1',
            'provider' => 'LGU',
            'school_year' => '2026-2026',
            'description' => 'Test description',
            'benefits' => 'Benefits',
            'available_slots' => 10,
            'application_start_date' => now()->format('Y-m-d'),
            'application_deadline' => now()->addDays(20)->format('Y-m-d'),
            'status' => 'open',
        ]);
        $response1->assertSessionHasErrors('school_year');

        // Testing non-consecutive span (2026-2029)
        $response2 = $this->actingAs($admin)->post('/admin/scholarships', [
            'name' => 'Invalid Grant 2',
            'provider' => 'LGU',
            'school_year' => '2026-2029',
            'description' => 'Test description',
            'benefits' => 'Benefits',
            'available_slots' => 10,
            'application_start_date' => now()->format('Y-m-d'),
            'application_deadline' => now()->addDays(20)->format('Y-m-d'),
            'status' => 'open',
        ]);
        $response2->assertSessionHasErrors('school_year');
    }

    public function test_admin_can_add_requirement_using_preset_and_coe_document()
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->post("/admin/scholarships/{$this->scholarship->id}/requirements", [
            'requirement_type' => 'document',
            'preset_name' => 'Certificate of Enrollment (COE)',
            'is_required' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('scholarship_requirements', [
            'scholarship_id' => $this->scholarship->id,
            'requirement_name' => 'Certificate of Enrollment (COE)',
            'requirement_type' => 'document',
        ]);
    }
}
