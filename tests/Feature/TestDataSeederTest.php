<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\TestDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestDataSeederTest extends TestCase
{
    public function test_seeder_populates_required_scholarships_and_100_students(): void
    {
        // 1. Scholarships must be TES and TDP only
        $scholarships = Scholarship::all();
        $this->assertCount(2, $scholarships);
        $names = $scholarships->pluck('name')->all();
        $this->assertContains('Tertiary Education Subsidy (TES)', $names);
        $this->assertContains('Tulong Dunong Program (TDP)', $names);

        // 2. Exactly 100 students created
        $studentUsers = User::where('role', 'student')->where('email', 'like', 'student%@csu.edu.ph')->get();
        $this->assertCount(100, $studentUsers);

        $students = Student::all();
        $this->assertGreaterThanOrEqual(100, $students->count());

        // 3. Divided equally across the 5 available courses (20 each)
        $expectedCourses = [
            'Bachelor of Science in Information Technology (BSIT)',
            'Bachelor of Science in Agriculture (BSA)',
            'Bachelor of Science in Hospitality Management (BSHM)',
            'Bachelor of Elementary Education (BEEd)',
            'Bachelor of Secondary Education (BSEd)',
        ];

        foreach ($expectedCourses as $course) {
            $count = Student::where('course', $course)->count();
            $this->assertEquals(20, $count, "Course '{$course}' must have exactly 20 students.");
        }

        // 4. At least 30 students applied to TES
        $tes = Scholarship::where('name', 'Tertiary Education Subsidy (TES)')->first();
        $this->assertNotNull($tes);
        $tesApplicantsCount = Application::where('scholarship_id', $tes->id)->count();
        $this->assertGreaterThanOrEqual(30, $tesApplicantsCount, "TES must have at least 30 student applicants.");

        // 5. At least 30 students applied to TDP
        $tdp = Scholarship::where('name', 'Tulong Dunong Program (TDP)')->first();
        $this->assertNotNull($tdp);
        $tdpApplicantsCount = Application::where('scholarship_id', $tdp->id)->count();
        $this->assertGreaterThanOrEqual(30, $tdpApplicantsCount, "TDP must have at least 30 student applicants.");

        // 6. Active scholars enrolled for approved applicants
        $activeScholars = Scholar::where('status', 'active')->count();
        $this->assertGreaterThanOrEqual(30, $activeScholars);
    }
}
