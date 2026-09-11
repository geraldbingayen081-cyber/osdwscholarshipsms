<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportTest extends TestCase
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

        AcademicYear::create([
            'name' => 'AY 2026-2027',
            'start_date' => '2026-08-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_access_reports_and_analytics()
    {
        $response = $this->actingAs($this->admin)->get('/admin/reports');

        $response->assertStatus(200);
        $response->assertSee('Reports & Analytics');
        $response->assertSee('Program Slot Utilization');
    }
}
