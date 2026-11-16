<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentPasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;
    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentUser = User::create([
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'last_name' => 'Reyes',
            'email' => 'maria.reyes@csu.edu.ph',
            'password' => Hash::make('old_password123'),
            'role' => 'student',
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_number' => '26-12345',
            'course' => 'Bachelor of Science in Information Technology (BSIT)',
            'year_level' => '3rd Year',
            'contact_number' => '+639171234567',
        ]);
    }

    public function test_guest_cannot_update_student_password(): void
    {
        $response = $this->put(route('student.password.update'), [
            'current_password' => 'old_password123',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_student_can_change_password_with_valid_credentials(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->put(route('student.password.update'), [
                'current_password' => 'old_password123',
                'password' => 'new_secure_pass123',
                'password_confirmation' => 'new_secure_pass123',
            ]);

        $response->assertRedirect(route('student.profile.show'));
        $response->assertSessionHas('password_success');

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('new_secure_pass123', $this->studentUser->password));

        // Verify audit log
        $this->assertDatabaseHas('system_logs', [
            'module' => 'Security',
            'action' => 'password_change',
        ]);
    }

    public function test_student_cannot_change_password_with_incorrect_current_password(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->put(route('student.password.update'), [
                'current_password' => 'wrong_password',
                'password' => 'new_secure_pass123',
                'password_confirmation' => 'new_secure_pass123',
            ]);

        $response->assertSessionHasErrors(['current_password']);

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('old_password123', $this->studentUser->password));
    }

    public function test_student_cannot_change_password_if_confirmation_does_not_match(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->put(route('student.password.update'), [
                'current_password' => 'old_password123',
                'password' => 'new_secure_pass123',
                'password_confirmation' => 'different_pass123',
            ]);

        $response->assertSessionHasErrors(['password']);

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('old_password123', $this->studentUser->password));
    }

    public function test_student_cannot_change_password_shorter_than_8_characters(): void
    {
        $response = $this->actingAs($this->studentUser)
            ->put(route('student.password.update'), [
                'current_password' => 'old_password123',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertSessionHasErrors(['password']);

        $this->studentUser->refresh();
        $this->assertTrue(Hash::check('old_password123', $this->studentUser->password));
    }
}
