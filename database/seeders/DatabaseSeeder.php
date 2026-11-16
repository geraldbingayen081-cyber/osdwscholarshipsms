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
        $this->call(TestDataSeeder::class);
    }
}
