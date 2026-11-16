<?php

namespace Tests\Feature;

use App\Models\ApplicationDocument;
use App\Models\User;
use Tests\TestCase;

class DocumentViewerTest extends TestCase
{
    public function test_admin_can_download_and_view_application_document(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $doc = ApplicationDocument::first();
        $this->assertNotNull($doc);

        $response = $this->actingAs($admin)->get(route('admin.applications.documents.download', $doc->id));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_student_can_view_own_document(): void
    {
        $doc = ApplicationDocument::with('application.student.user')->first();
        $this->assertNotNull($doc);

        $studentUser = $doc->application->student->user;
        $this->assertNotNull($studentUser);

        $response = $this->actingAs($studentUser)->get(route('student.documents.view', $doc->id));
        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }
}
