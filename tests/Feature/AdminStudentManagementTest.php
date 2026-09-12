<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $studentUser;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Officer',
            'email' => 'admin@csu.edu.ph',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
        ]);

        $this->studentUser = User::create([
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@csu.edu.ph',
            'password' => Hash::make('originalpassword123'),
            'role' => 'student',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_number' => '2024-00123',
            'college' => 'CICS',
            'program' => 'BS Information Technology (BSIT)',
            'course' => 'BS Information Technology (BSIT)',
            'year_level' => '2nd Year',
            'contact_number' => '09171234567',
            'current_gwa' => 1.75,
            'monthly_household_income' => 15000,
            'is_4ps' => false,
            'municipality' => 'Lal-lo',
            'barangay' => 'Centro',
        ]);
    }

    public function test_admin_can_view_student_profile()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.students.show', $this->student->id));

        $response->assertStatus(200);
        $response->assertSee('Juan Dela Cruz');
        $response->assertSee('2024-00123');
        $response->assertSee('Manage Student Profile &amp; Password', false);
    }

    public function test_admin_can_update_student_personal_information()
    {
        $response = $this->actingAs($this->admin)->put(route('admin.students.update', $this->student->id), [
            'first_name' => 'Juan Carlos',
            'middle_name' => 'Reyes',
            'last_name' => 'Dela Cruz Jr.',
            'email' => 'juancarlos@csu.edu.ph',
            'student_number' => '2024-00999',
            'college' => 'CTE',
            'program' => 'Bachelor of Secondary Education - English (BSEd-ENG)',
            'course' => 'Bachelor of Secondary Education - English (BSEd-ENG)',
            'year_level' => '3rd Year',
            'contact_number' => '09987654321',
            'current_gwa' => 1.50,
            'monthly_household_income' => 20000,
            'is_4ps' => '1',
            'municipality' => 'Gattaran',
            'barangay' => 'Nassiping',
        ]);

        $response->assertRedirect(route('admin.students.show', $this->student->id));
        $response->assertSessionHas('success');

        $this->studentUser->refresh();
        $this->assertEquals('Juan Carlos', $this->studentUser->first_name);
        $this->assertEquals('Reyes', $this->studentUser->middle_name);
        $this->assertEquals('Dela Cruz Jr.', $this->studentUser->last_name);
        $this->assertEquals('juancarlos@csu.edu.ph', $this->studentUser->email);

        $this->student->refresh();
        $this->assertEquals('2024-00999', $this->student->student_number);
        $this->assertEquals('CTE', $this->student->college->value ?? $this->student->college);
        $this->assertEquals('Bachelor of Secondary Education - English (BSEd-ENG)', $this->student->program);
        $this->assertEquals('3rd Year', $this->student->year_level);
        $this->assertEquals('09987654321', $this->student->contact_number);
        $this->assertEquals(1.50, (float)$this->student->current_gwa);
        $this->assertEquals(20000, (float)$this->student->monthly_household_income);
        $this->assertTrue((bool)$this->student->is_4ps);
        $this->assertEquals('Gattaran', $this->student->municipality);
        $this->assertEquals('Nassiping', $this->student->barangay);

        // Verify password remained unchanged
        $this->assertTrue(Hash::check('originalpassword123', $this->studentUser->password));
    }

    public function test_admin_can_reset_student_password_without_current_password()
    {
        $response = $this->actingAs($this->admin)->put(route('admin.students.update', $this->student->id), [
            'first_name' => 'Juan',
            'middle_name' => 'Santos',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@csu.edu.ph',
            'student_number' => '2024-00123',
            'college' => 'CICS',
            'program' => 'BS Information Technology (BSIT)',
            'course' => 'BS Information Technology (BSIT)',
            'year_level' => '2nd Year',
            'password' => 'newSecretPassword2026',
            'password_confirmation' => 'newSecretPassword2026',
        ]);

        $response->assertRedirect(route('admin.students.show', $this->student->id));
        $response->assertSessionHas('success');

        $this->studentUser->refresh();
        // New password is active
        $this->assertTrue(Hash::check('newSecretPassword2026', $this->studentUser->password));
        // Old password no longer works
        $this->assertFalse(Hash::check('originalpassword123', $this->studentUser->password));
    }

    public function test_password_confirmation_mismatch_fails_validation()
    {
        $response = $this->actingAs($this->admin)->put(route('admin.students.update', $this->student->id), [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@csu.edu.ph',
            'student_number' => '2024-00123',
            'password' => 'newSecretPassword2026',
            'password_confirmation' => 'mismatchedPassword',
        ]);

        $response->assertSessionHasErrors(['password']);
        
        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('originalpassword123', $this->studentUser->password));
    }

    public function test_student_cannot_access_admin_student_update_route()
    {
        $response = $this->actingAs($this->studentUser)->put(route('admin.students.update', $this->student->id), [
            'first_name' => 'Hacker',
            'last_name' => 'Student',
            'email' => 'hacker@csu.edu.ph',
            'student_number' => '2024-00123',
        ]);

        $response->assertStatus(403);
    }
}
