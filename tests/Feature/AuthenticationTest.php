<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('OSDW-Scholarship Management System', false);
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Student Account Registration');
    }

    public function test_student_can_register_with_valid_philippine_contact_number()
    {
        $response = $this->post('/register', [
            'first_name' => 'Maria',
            'middle_name' => 'Clara',
            'last_name' => 'Santos',
            'email' => 'maria@csu.edu.ph',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'student_number' => '26-32424',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '1st Year',
            'contact_number' => '9123456789',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'maria@csu.edu.ph')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);
        $this->assertEquals('Maria Clara Santos', $user->full_name);

        $student = Student::where('user_id', $user->id)->first();
        $this->assertNotNull($student);
        $this->assertEquals('+639123456789', $student->contact_number);
    }

    public function test_registration_rejects_invalid_contact_number()
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testinvalid@csu.edu.ph',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'student_number' => '26-32425',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '1st Year',
            'contact_number' => '0912345678', // Invalid: 0 prefix instead of 9XXXXXXXXX
        ]);

        $response->assertSessionHasErrors('contact_number');
    }

    public function test_admin_login_redirects_to_admin_dashboard()
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@csu.edu.ph',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_student_cannot_access_admin_dashboard()
    {
        $studentUser = User::create([
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($studentUser)->get('/admin/dashboard');

        $response->assertRedirect('/student/dashboard');
    }

    public function test_student_can_login_with_student_id()
    {
        $user = User::create([
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'email' => 'juan.student@csu.edu.ph',
            'password' => bcrypt('securePassword123!'),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'student_number' => '26-32424',
            'course' => 'BSIT',
            'year_level' => '1st Year',
            'contact_number' => '+639123456789',
        ]);

        $response = $this->post('/login', [
            'login_id' => '26-32424',
            'password' => 'securePassword123!',
        ]);

        $response->assertRedirect('/student/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_screen_displays_student_id_label_and_input()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Student ID', false);
        $response->assertSee('name="login_id"', false);
    }

    public function test_authenticated_student_visiting_login_page_is_redirected_to_dashboard()
    {
        $studentUser = User::create([
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student.nav@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($studentUser)->get('/login');
        $response->assertRedirect(route('student.dashboard'));
    }

    public function test_authenticated_admin_visiting_login_page_is_redirected_to_dashboard()
    {
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin.nav@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($adminUser)->get('/login');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_unauthenticated_user_cannot_access_dashboard_and_is_redirected_to_login()
    {
        $resAdmin = $this->get('/admin/dashboard');
        $resAdmin->assertRedirect('/login');

        $resStudent = $this->get('/student/dashboard');
        $resStudent->assertRedirect('/login');
    }

    public function test_protected_responses_contain_no_cache_headers()
    {
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin.cache@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($adminUser)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertHeader('Cache-Control');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', $response->headers->get('Cache-Control'));
    }

    public function test_user_can_logout_and_is_redirected_to_login()
    {
        $user = User::create([
            'first_name' => 'User',
            'last_name' => 'Test',
            'email' => 'logout.test@csu.edu.ph',
            'password' => bcrypt('password123'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
