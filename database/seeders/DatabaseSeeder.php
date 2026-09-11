<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Authorized Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@csu.edu.ph'],
            [
                'first_name' => 'Administrator',
                'middle_name' => 'OSDW',
                'last_name' => 'System',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // 2. Seed Sample Student Account for testing
        $studentUser = User::firstOrCreate(
            ['email' => 'student@csu.edu.ph'],
            [
                'first_name' => 'Ryan',
                'middle_name' => 'Carlo',
                'last_name' => 'DeJesus',
                'password' => Hash::make('password123'),
                'role' => 'student',
            ]
        );

        Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'student_number' => '26-02001',
                'course' => 'Bachelor of Science in Information Technology (BSIT)',
                'year_level' => '3rd Year',
                'contact_number' => '+639171234567',
            ]
        );

        // 3. Seed Academic Year
        $ay = AcademicYear::firstOrCreate(
            ['name' => 'AY 2026-2027'],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2027-06-30',
                'status' => 'active',
            ]
        );

        // 4. Seed Semesters
        $sem1 = Semester::firstOrCreate(
            ['academic_year_id' => $ay->id, 'name' => 'First Semester'],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2026-12-20',
                'status' => 'active',
            ]
        );

        $sem2 = Semester::firstOrCreate(
            ['academic_year_id' => $ay->id, 'name' => 'Second Semester'],
            [
                'start_date' => '2027-01-10',
                'end_date' => '2027-05-30',
                'status' => 'inactive',
            ]
        );

        // 5. Seed Sample Scholarships
        $scholarship1 = Scholarship::firstOrCreate(
            ['name' => 'CSU Academic Honor Scholarship'],
            [
                'academic_year_id' => $ay->id,
                'semester_id' => $sem1->id,
                'provider' => 'Cagayan State University – OSDW',
                'description' => 'Merit-based scholarship awarded to top-performing students of CSU–Lal-lo Campus with high academic standing.',
                'benefits' => '100% Tuition Fee Discount + Monthly Book Allowance of PHP 2,500',
                'available_slots' => 30,
                'application_start_date' => '2026-08-01',
                'application_deadline' => '2026-10-31',
                'coverage_type' => 'semester',
                'status' => 'open',
                'created_by' => $admin->id,
            ]
        );

        ScholarshipRequirement::firstOrCreate([
            'scholarship_id' => $scholarship1->id,
            'requirement_name' => 'Must have a GWA of 1.75 or higher with no grade lower than 2.0',
            'requirement_type' => 'eligibility',
            'is_required' => true,
        ]);

        ScholarshipRequirement::firstOrCreate([
            'scholarship_id' => $scholarship1->id,
            'requirement_name' => 'Certificate of Grades (COG) signed by Campus Registrar',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        ScholarshipRequirement::firstOrCreate([
            'scholarship_id' => $scholarship1->id,
            'requirement_name' => 'Certificate of Good Moral Character',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        ScholarshipRequirement::firstOrCreate([
            'scholarship_id' => $scholarship1->id,
            'requirement_name' => 'Valid Student ID / COR',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        // Second Scholarship
        $scholarship2 = Scholarship::firstOrCreate(
            ['name' => 'Tulong Dunong Program (TDP-CHED)'],
            [
                'academic_year_id' => $ay->id,
                'semester_id' => $sem1->id,
                'provider' => 'Commission on Higher Education (CHED)',
                'description' => 'Financial assistance grant for qualified college students residing in Cagayan Valley.',
                'benefits' => 'Stipend of PHP 7,500 per semester',
                'available_slots' => 50,
                'application_start_date' => '2026-08-01',
                'application_deadline' => '2026-11-15',
                'coverage_type' => 'semester',
                'status' => 'open',
                'created_by' => $admin->id,
            ]
        );

        ScholarshipRequirement::firstOrCreate([
            'scholarship_id' => $scholarship2->id,
            'requirement_name' => 'Must be a bona fide student of CSU Lal-lo Campus',
            'requirement_type' => 'eligibility',
            'is_required' => true,
        ]);

        ScholarshipRequirement::firstOrCreate([
            'scholarship_id' => $scholarship2->id,
            'requirement_name' => 'Latest Certificate of Tax Exemption / ITR of Parents',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);

        ScholarshipRequirement::firstOrCreate([
            'scholarship_id' => $scholarship2->id,
            'requirement_name' => 'Certificate of Registration (COR)',
            'requirement_type' => 'document',
            'is_required' => true,
        ]);
    }
}
