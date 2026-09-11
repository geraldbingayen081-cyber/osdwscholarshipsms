<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ComplianceRequest;
use App\Models\ComplianceRequirement;
use App\Models\Scholar;
use App\Models\ScholarCompliance;
use App\Models\ScholarComplianceDocument;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComplianceRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private User $studentUser;
    private Student $student;
    private Scholarship $scholarship;
    private Scholar $scholar;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

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
            'student_number' => '26-10001',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'contact_number' => '+639171112233',
        ]);

        $ay = AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);

        $this->scholarship = Scholarship::create([
            'name' => 'CHED Tulong Dunong Program (TDP)',
            'provider' => 'CHED',
            'description' => 'Continuing subsidy for qualified tertiary students.',
            'coverage_type' => 'continuing',
            'benefits' => 'PHP 15,000 / semester',
            'available_slots' => 100,
            'application_start_date' => '2026-08-01',
            'application_deadline' => '2026-12-31',
            'academic_year_id' => $ay->id,
            'status' => 'open',
            'created_by' => $this->adminUser->id,
        ]);

        $application = \App\Models\Application::create([
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'status' => 'approved',
        ]);

        $this->scholar = Scholar::create([
            'student_id' => $this->student->id,
            'scholarship_id' => $this->scholarship->id,
            'application_id' => $application->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_create_and_publish_compliance_request_auto_assigning_to_active_scholars()
    {
        $response = $this->actingAs($this->adminUser)->post('/admin/compliance', [
            'scholarship_id' => $this->scholarship->id,
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'deadline' => date('Y-m-d', strtotime('+14 days')),
            'title' => '1st Sem AY 2026-2027 TDP Compliance',
            'instructions' => 'Please upload clear scanned copies of your COE and ID.',
            'requirements' => [
                ['name' => 'Certificate of Enrollment (COE)', 'instruction' => 'Stamped by Registrar', 'is_required' => 1],
                ['name' => 'Photocopy of Valid School ID', 'instruction' => 'Front and back', 'is_required' => 1],
            ],
        ]);

        $complianceRequest = ComplianceRequest::first();
        $this->assertNotNull($complianceRequest);
        $this->assertEquals('1st Sem AY 2026-2027 TDP Compliance', $complianceRequest->title);
        $this->assertEquals(2, $complianceRequest->requirements()->count());

        // Scholar must be automatically assigned
        $this->assertDatabaseHas('scholar_compliances', [
            'compliance_request_id' => $complianceRequest->id,
            'scholar_id' => $this->scholar->id,
            'status' => 'not_submitted',
        ]);

        $response->assertRedirect(route('admin.compliance.show', $complianceRequest->id));
    }

    public function test_student_sees_compliance_request_on_dashboard_and_can_submit_documents()
    {
        $complianceRequest = ComplianceRequest::create([
            'scholarship_id' => $this->scholarship->id,
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'title' => 'TDP Mid-Year Compliance',
            'deadline' => date('Y-m-d', strtotime('+10 days')),
            'status' => 'active',
            'created_by' => $this->adminUser->id,
        ]);

        $req1 = ComplianceRequirement::create([
            'compliance_request_id' => $complianceRequest->id,
            'name' => 'Certificate of Enrollment (COE)',
            'is_required' => true,
        ]);

        $req2 = ComplianceRequirement::create([
            'compliance_request_id' => $complianceRequest->id,
            'name' => 'Photocopy of School ID',
            'is_required' => true,
        ]);

        $scholarCompliance = ScholarCompliance::create([
            'compliance_request_id' => $complianceRequest->id,
            'scholar_id' => $this->scholar->id,
            'status' => 'not_submitted',
        ]);

        // Student visits dashboard
        $response = $this->actingAs($this->studentUser)->get('/student/dashboard');
        $response->assertStatus(200);
        $response->assertSee('TDP Mid-Year Compliance');

        // Student submits first document (COE)
        $file1 = UploadedFile::fake()->create('coe_document.pdf', 800, 'application/pdf');

        $uploadResponse = $this->actingAs($this->studentUser)
            ->post("/student/compliance/{$complianceRequest->id}/requirements/{$req1->id}", [
                'document_file' => $file1,
                'student_remarks' => 'Enrolled with 21 units this semester.',
            ]);

        $uploadResponse->assertSessionHas('success');

        $this->assertDatabaseHas('scholar_compliance_documents', [
            'scholar_compliance_id' => $scholarCompliance->id,
            'compliance_requirement_id' => $req1->id,
            'original_filename' => 'coe_document.pdf',
            'verification_status' => 'pending',
        ]);

        // Overall status should now be partially_submitted
        $scholarCompliance->refresh();
        $this->assertEquals('partially_submitted', $scholarCompliance->status);
    }

    public function test_admin_can_verify_documents_and_auto_complete_scholar_compliance()
    {
        $complianceRequest = ComplianceRequest::create([
            'scholarship_id' => $this->scholarship->id,
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'title' => 'TDP Mid-Year Compliance',
            'deadline' => date('Y-m-d', strtotime('+10 days')),
            'status' => 'active',
            'created_by' => $this->adminUser->id,
        ]);

        $req1 = ComplianceRequirement::create([
            'compliance_request_id' => $complianceRequest->id,
            'name' => 'Certificate of Enrollment (COE)',
            'is_required' => true,
        ]);

        $scholarCompliance = ScholarCompliance::create([
            'compliance_request_id' => $complianceRequest->id,
            'scholar_id' => $this->scholar->id,
            'status' => 'submitted',
        ]);

        $doc1 = ScholarComplianceDocument::create([
            'scholar_compliance_id' => $scholarCompliance->id,
            'compliance_requirement_id' => $req1->id,
            'file_path' => 'compliance_documents/1/test_coe.pdf',
            'original_filename' => 'test_coe.pdf',
            'verification_status' => 'pending',
        ]);

        // Admin verifies document
        $response = $this->actingAs($this->adminUser)
            ->post("/admin/compliance-documents/{$doc1->id}/verify", [
                'verification_status' => 'verified',
            ]);

        $response->assertSessionHas('success');

        $doc1->refresh();
        $this->assertEquals('verified', $doc1->verification_status);

        $scholarCompliance->refresh();
        // Since all (1 of 1) requirements are verified, scholar compliance status is completed
        $this->assertEquals('completed', $scholarCompliance->status);

        // Scholar's main scholarship standing remains active
        $this->scholar->refresh();
        $this->assertEquals('active', $this->scholar->status);
    }

    public function test_admin_can_mark_needs_correction_and_student_can_resubmit()
    {
        $complianceRequest = ComplianceRequest::create([
            'scholarship_id' => $this->scholarship->id,
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'title' => 'TDP Mid-Year Compliance',
            'deadline' => date('Y-m-d', strtotime('+10 days')),
            'status' => 'active',
            'created_by' => $this->adminUser->id,
        ]);

        $req = ComplianceRequirement::create([
            'compliance_request_id' => $complianceRequest->id,
            'name' => 'Certificate of Enrollment (COE)',
            'is_required' => true,
        ]);

        $scholarCompliance = ScholarCompliance::create([
            'compliance_request_id' => $complianceRequest->id,
            'scholar_id' => $this->scholar->id,
            'status' => 'submitted',
        ]);

        $doc = ScholarComplianceDocument::create([
            'scholar_compliance_id' => $scholarCompliance->id,
            'compliance_requirement_id' => $req->id,
            'file_path' => 'compliance_documents/1/blurry_coe.pdf',
            'original_filename' => 'blurry_coe.pdf',
            'verification_status' => 'pending',
        ]);

        // Admin marks as needs_correction with remark
        $this->actingAs($this->adminUser)
            ->post("/admin/compliance-documents/{$doc->id}/verify", [
                'verification_status' => 'needs_correction',
                'admin_remarks' => 'The uploaded copy is blurry and illegible. Please provide a clear scan.',
            ]);

        $doc->refresh();
        $this->assertEquals('needs_correction', $doc->verification_status);
        $this->assertEquals('The uploaded copy is blurry and illegible. Please provide a clear scan.', $doc->admin_remarks);

        $scholarCompliance->refresh();
        $this->assertEquals('needs_correction', $scholarCompliance->status);

        // Student visits show page and sees remark
        $pageResponse = $this->actingAs($this->studentUser)->get("/student/compliance/{$complianceRequest->id}");
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('The uploaded copy is blurry and illegible. Please provide a clear scan.');

        // Student resubmits replacement file
        $replacementFile = UploadedFile::fake()->create('clear_coe.pdf', 600, 'application/pdf');

        $resubmitResponse = $this->actingAs($this->studentUser)
            ->post("/student/compliance/{$complianceRequest->id}/requirements/{$req->id}", [
                'document_file' => $replacementFile,
                'student_remarks' => 'Resubmitted clear scan from registrar.',
            ]);

        $resubmitResponse->assertSessionHas('success');

        $doc->refresh();
        $this->assertEquals('clear_coe.pdf', $doc->original_filename);
        $this->assertEquals('pending', $doc->verification_status);
        $this->assertNull($doc->admin_remarks); // previous remark cleared

        $scholarCompliance->refresh();
        $this->assertEquals('under_review', $scholarCompliance->status);
    }
}
