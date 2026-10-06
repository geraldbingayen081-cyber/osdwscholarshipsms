<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use App\Models\WelfareCase;
use App\Models\WelfareCaseDocument;
use App\Models\WelfareCaseReferral;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds for 10 complete students with scholarship and welfare cases.
     */
    public function run(): void
    {
        // Ensure Admin user exists
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

        // Ensure Academic Year & Semester exist
        $ay = AcademicYear::firstOrCreate(
            ['name' => 'AY 2026-2027'],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2027-06-30',
                'status' => 'active',
            ]
        );

        $sem = Semester::firstOrCreate(
            ['academic_year_id' => $ay->id, 'name' => 'First Semester'],
            [
                'start_date' => '2026-08-01',
                'end_date' => '2026-12-20',
                'status' => 'active',
            ]
        );

        // Ensure Scholarships exist
        $scholarshipTDP = Scholarship::firstOrCreate(
            ['name' => 'Tulong Dunong Program (TDP-CHED)'],
            [
                'academic_year_id' => $ay->id,
                'semester_id' => $sem->id,
                'provider' => 'Commission on Higher Education (CHED)',
                'description' => 'Financial assistance grant for qualified college students residing in Cagayan Valley.',
                'benefits' => 'PHP 7,500 stipend per semester',
                'available_slots' => 50,
                'application_start_date' => '2026-08-01',
                'application_deadline' => '2026-11-15',
                'coverage_type' => 'semester',
                'status' => 'open',
                'created_by' => $admin->id,
            ]
        );

        $scholarshipLGU = Scholarship::firstOrCreate(
            ['name' => 'LGU Lal-lo Municipal Scholarship'],
            [
                'academic_year_id' => $ay->id,
                'semester_id' => $sem->id,
                'provider' => 'Local Government Unit of Lal-lo',
                'description' => 'Educational assistance program funded by the municipal government for resident students.',
                'benefits' => 'PHP 5,000 allowance per semester',
                'available_slots' => 40,
                'application_start_date' => '2026-08-01',
                'application_deadline' => '2026-11-30',
                'coverage_type' => 'semester',
                'status' => 'open',
                'created_by' => $admin->id,
            ]
        );

        $scholarshipAcademic = Scholarship::firstOrCreate(
            ['name' => 'CSU Academic Honor Scholarship'],
            [
                'academic_year_id' => $ay->id,
                'semester_id' => $sem->id,
                'provider' => 'Cagayan State University – OSDW',
                'description' => 'Institutional grant for top performing academic achievers.',
                'benefits' => '100% Tuition Fee Discount + Monthly Book Allowance of PHP 2,500',
                'available_slots' => 30,
                'application_start_date' => '2026-08-01',
                'application_deadline' => '2026-10-31',
                'coverage_type' => 'semester',
                'status' => 'open',
                'created_by' => $admin->id,
            ]
        );

        // 10 Distinct Filipino Student Profiles
        $studentsData = [
            [
                'first_name' => 'Juan',
                'middle_name' => 'Carlos',
                'last_name' => 'Dela Cruz',
                'email' => 'juan.delacruz@csu.edu.ph',
                'student_number' => '24-00101',
                'sex' => 'Male',
                'college' => 'CICS',
                'course' => 'BS Information Technology (BSIT)',
                'year_level' => '3rd Year',
                'contact_number' => '09171234501',
                'current_gwa' => 1.45,
                'monthly_household_income' => 12500.00,
                'municipality' => 'Lal-lo',
                'barangay' => 'Bagumbayan',
                'is_4ps' => true,
                'app_status' => 'approved',
                'scholarship' => $scholarshipTDP,
                'welfare_category' => 'Financial Assistance',
                'welfare_desc' => 'Requested financial support for boarding house expenses and internet connectivity requirements.',
                'welfare_status' => 'Resolved',
                'refer_type' => 'scholarship',
                'refer_to' => null,
            ],
            [
                'first_name' => 'Maria Kristina',
                'middle_name' => 'Santos',
                'last_name' => 'Aquino',
                'email' => 'maria.aquino@csu.edu.ph',
                'student_number' => '24-00102',
                'sex' => 'Female',
                'college' => 'CHM',
                'course' => 'BS Hospitality Management (BSHM)',
                'year_level' => '2nd Year',
                'contact_number' => '09171234502',
                'current_gwa' => 1.30,
                'monthly_household_income' => 9800.00,
                'municipality' => 'Lal-lo',
                'barangay' => 'Centro',
                'is_4ps' => true,
                'app_status' => 'approved',
                'scholarship' => $scholarshipAcademic,
                'welfare_category' => 'Financial Assistance',
                'welfare_desc' => 'Single-parent household experiencing difficulty affording textbook and project materials.',
                'welfare_status' => 'Resolved',
                'refer_type' => 'scholarship',
                'refer_to' => null,
            ],
            [
                'first_name' => 'Mark Anthony',
                'middle_name' => 'Ramos',
                'last_name' => 'Bautista',
                'email' => 'mark.bautista@csu.edu.ph',
                'student_number' => '25-00103',
                'sex' => 'Male',
                'college' => 'COA',
                'course' => 'BS Agriculture (Crop Science)',
                'year_level' => '2nd Year',
                'contact_number' => '09171234503',
                'current_gwa' => 1.85,
                'monthly_household_income' => 8000.00,
                'municipality' => 'Gattaran',
                'barangay' => 'Naguilian',
                'is_4ps' => false,
                'app_status' => 'approved',
                'scholarship' => $scholarshipLGU,
                'welfare_category' => 'Emergency Relief',
                'welfare_desc' => 'Family crops affected by recent flooding; needed emergency educational allowance.',
                'welfare_status' => 'Closed',
                'refer_type' => 'office',
                'refer_to' => 'Disaster Risk & Relief Unit',
            ],
            [
                'first_name' => 'Angelica',
                'middle_name' => 'Mae',
                'last_name' => 'Soriano',
                'email' => 'angelica.soriano@csu.edu.ph',
                'student_number' => '23-00104',
                'sex' => 'Female',
                'college' => 'CTED',
                'course' => 'Bachelor of Secondary Education - English (BSEd-ENG)',
                'year_level' => '4th Year',
                'contact_number' => '09171234504',
                'current_gwa' => 1.25,
                'monthly_household_income' => 14000.00,
                'municipality' => 'Allacapan',
                'barangay' => 'Binaratan',
                'is_4ps' => false,
                'app_status' => 'approved',
                'scholarship' => $scholarshipAcademic,
                'welfare_category' => 'Academic Hardship',
                'welfare_desc' => 'Incurred high expenses for off-campus practice teaching and instructional materials.',
                'welfare_status' => 'Referred',
                'refer_type' => 'office',
                'refer_to' => 'Dean of Teacher Education',
            ],
            [
                'first_name' => 'Joshua',
                'middle_name' => 'Paul',
                'last_name' => 'Mendoza',
                'email' => 'joshua.mendoza@csu.edu.ph',
                'student_number' => '25-00105',
                'sex' => 'Male',
                'college' => 'CICS',
                'course' => 'BS Information Technology (BSIT)',
                'year_level' => '1st Year',
                'contact_number' => '09171234505',
                'current_gwa' => 1.95,
                'monthly_household_income' => 11000.00,
                'municipality' => 'Lal-lo',
                'barangay' => 'Maxingal',
                'is_4ps' => true,
                'app_status' => 'under_review',
                'scholarship' => $scholarshipTDP,
                'welfare_category' => 'Health / Medical Concern',
                'welfare_desc' => 'Medical check-up needed for physical diagnostic requirements in student athlete training.',
                'welfare_status' => 'Referred',
                'refer_type' => 'office',
                'refer_to' => 'Campus Health Services / Clinic',
            ],
            [
                'first_name' => 'Princess Diane',
                'middle_name' => 'Gomez',
                'last_name' => 'Perez',
                'email' => 'princess.perez@csu.edu.ph',
                'student_number' => '24-00106',
                'sex' => 'Female',
                'college' => 'CHM',
                'course' => 'BS Hospitality Management (BSHM)',
                'year_level' => '3rd Year',
                'contact_number' => '09171234506',
                'current_gwa' => 1.60,
                'monthly_household_income' => 13500.00,
                'municipality' => 'Camalaniugan',
                'barangay' => 'Dugo',
                'is_4ps' => false,
                'app_status' => 'approved',
                'scholarship' => $scholarshipLGU,
                'welfare_category' => 'Financial Assistance',
                'welfare_desc' => 'Requires uniform and culinary laboratory toolkit subsidy for kitchen practicum.',
                'welfare_status' => 'Under Assessment',
                'refer_type' => 'scholarship',
                'refer_to' => null,
            ],
            [
                'first_name' => 'Christian Jay',
                'middle_name' => 'Torres',
                'last_name' => 'Navarro',
                'email' => 'christian.navarro@csu.edu.ph',
                'student_number' => '25-00107',
                'sex' => 'Male',
                'college' => 'CICS',
                'course' => 'BS Information Technology (BSIT)',
                'year_level' => '2nd Year',
                'contact_number' => '09171234507',
                'current_gwa' => 2.10,
                'monthly_household_income' => 15000.00,
                'municipality' => 'Lal-lo',
                'barangay' => 'Sta. Maria',
                'is_4ps' => false,
                'app_status' => 'submitted',
                'scholarship' => $scholarshipTDP,
                'welfare_category' => 'Academic Hardship',
                'welfare_desc' => 'Difficulty keeping up with computer programming lab assignments; requested peer tutoring assistance.',
                'welfare_status' => 'Open',
                'refer_type' => 'office',
                'refer_to' => 'Guidance and Counseling Center',
            ],
            [
                'first_name' => 'Bea Nicole',
                'middle_name' => 'Reyes',
                'last_name' => 'Castro',
                'email' => 'bea.castro@csu.edu.ph',
                'student_number' => '26-00108',
                'sex' => 'Female',
                'college' => 'CTED',
                'course' => 'Bachelor of Elementary Education (BEEd)',
                'year_level' => '1st Year',
                'contact_number' => '09171234508',
                'current_gwa' => 1.70,
                'monthly_household_income' => 7500.00,
                'municipality' => 'Aparri',
                'barangay' => 'Toran',
                'is_4ps' => true,
                'app_status' => 'approved',
                'scholarship' => $scholarshipTDP,
                'welfare_category' => 'Family / Personal Crisis',
                'welfare_desc' => 'Loss of family primary breadwinner; requested guidance session and urgent scholarship endorsement.',
                'welfare_status' => 'For Follow-up',
                'refer_type' => 'office',
                'refer_to' => 'Guidance and Counseling Center',
            ],
            [
                'first_name' => 'Geraldine',
                'middle_name' => 'Flores',
                'last_name' => 'Villanueva',
                'email' => 'geraldine.villanueva@csu.edu.ph',
                'student_number' => '24-00109',
                'sex' => 'Female',
                'college' => 'CTED',
                'course' => 'Bachelor of Secondary Education - Mathematics (BSEd-MATH)',
                'year_level' => '3rd Year',
                'contact_number' => '09171234509',
                'current_gwa' => 1.55,
                'monthly_household_income' => 10200.00,
                'municipality' => 'Lal-lo',
                'barangay' => 'Alucao',
                'is_4ps' => false,
                'app_status' => 'approved',
                'scholarship' => $scholarshipAcademic,
                'welfare_category' => 'Health / Medical Concern',
                'welfare_desc' => 'Prescription eyewear replacement needed for reading board work and study modules.',
                'welfare_status' => 'Resolved',
                'refer_type' => 'office',
                'refer_to' => 'Campus Health Services',
            ],
            [
                'first_name' => 'Kenneth',
                'middle_name' => 'Dan',
                'last_name' => 'Pascual',
                'email' => 'kenneth.pascual@csu.edu.ph',
                'student_number' => '23-00110',
                'sex' => 'Male',
                'college' => 'COA',
                'course' => 'BS Agriculture (Animal Science)',
                'year_level' => '4th Year',
                'contact_number' => '09171234510',
                'current_gwa' => 2.40,
                'monthly_household_income' => 18000.00,
                'municipality' => 'Buguey',
                'barangay' => 'Villa Leonora',
                'is_4ps' => false,
                'app_status' => 'rejected',
                'scholarship' => $scholarshipAcademic,
                'welfare_category' => 'Academic Hardship',
                'welfare_desc' => 'Under academic probation due to incomplete thesis field trial requirements.',
                'welfare_status' => 'Open',
                'refer_type' => 'office',
                'refer_to' => 'College Academic Adviser',
            ],
        ];

        foreach ($studentsData as $idx => $s) {
            // 1. Create or update Student User
            $user = User::updateOrCreate(
                ['email' => $s['email']],
                [
                    'first_name' => $s['first_name'],
                    'middle_name' => $s['middle_name'],
                    'last_name' => $s['last_name'],
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                ]
            );

            // 2. Create or update Student Profile
            $student = Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'student_number' => $s['student_number'],
                    'sex' => $s['sex'],
                    'college' => $s['college'],
                    'course' => $s['course'],
                    'program' => $s['course'],
                    'year_level' => $s['year_level'],
                    'contact_number' => $s['contact_number'],
                    'current_gwa' => $s['current_gwa'],
                    'monthly_household_income' => $s['monthly_household_income'],
                    'municipality' => $s['municipality'],
                    'barangay' => $s['barangay'],
                    'is_4ps' => $s['is_4ps'],
                ]
            );

            // 3. Create Scholarship Application
            $application = Application::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'scholarship_id' => $s['scholarship']->id,
                ],
                [
                    'status' => $s['app_status'],
                    'student_gwa' => $s['current_gwa'],
                    'monthly_income' => $s['monthly_household_income'],
                    'submitted_at' => now()->subDays(15 - $idx),
                    'remarks' => $s['app_status'] === 'approved' 
                        ? 'Passed qualification screening and academic requirement threshold.' 
                        : ($s['app_status'] === 'rejected' ? 'GWA does not satisfy minimum academic honor threshold (1.75).' : 'Documents currently being evaluated.'),
                ]
            );

            // 4. If approved, record as Active Scholar
            if ($s['app_status'] === 'approved') {
                Scholar::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'scholarship_id' => $s['scholarship']->id,
                    ],
                    [
                        'application_id' => $application->id,
                        'status' => 'active',
                        'approved_at' => now()->subDays(10 - $idx),
                    ]
                );
            }

            // 5. Create Student Welfare Case
            $caseControlId = 'WC-2026-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
            $welfareCase = WelfareCase::updateOrCreate(
                ['case_id' => $caseControlId],
                [
                    'student_id' => $student->id,
                    'category' => $s['welfare_category'],
                    'description' => $s['welfare_desc'],
                    'requested_information' => 'Follow up on support documentation and evaluate for campus welfare programs.',
                    'status' => $s['welfare_status'],
                    'created_at' => now()->subDays(20 - $idx),
                    'updated_at' => in_array($s['welfare_status'], ['Resolved', 'Closed']) ? now()->subDays(2) : now(),
                ]
            );

            // 6. Create Welfare Case Referrals
            if ($s['refer_type'] === 'scholarship') {
                WelfareCaseReferral::updateOrCreate(
                    [
                        'welfare_case_id' => $welfareCase->id,
                        'referral_type' => 'scholarship',
                    ],
                    [
                        'scholarship_id' => $s['scholarship']->id,
                        'referred_to_office' => null,
                        'referral_note' => 'Evaluated and officially endorsed for financial grant support under ' . $s['scholarship']->name,
                    ]
                );
            } elseif ($s['refer_to']) {
                WelfareCaseReferral::updateOrCreate(
                    [
                        'welfare_case_id' => $welfareCase->id,
                        'referred_to_office' => $s['refer_to'],
                    ],
                    [
                        'scholarship_id' => null,
                        'referral_type' => 'office',
                        'referral_note' => 'Referred for specialized assistance and intervention to ' . $s['refer_to'],
                    ]
                );
            }

            // 7. Create Welfare Case Sample Document Checklist entry
            WelfareCaseDocument::updateOrCreate(
                [
                    'welfare_case_id' => $welfareCase->id,
                    'file_name' => 'Official_Case_Intake_Assessment_' . $s['student_number'] . '.pdf',
                ],
                [
                    'file_path' => 'welfare_cases/sample_intake.pdf',
                ]
            );
        }
    }
}
