<?php

namespace Database\Seeders;

use App\Enums\College;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarshipRequirement;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use App\Models\WelfareCase;
use App\Models\WelfareCaseDocument;
use App\Models\WelfareCaseReferral;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Database\Seeders\SampleDocumentGenerator;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds:
     * - Scholarships: TES and TDP only.
     * - 100 Student accounts divided equally across 5 degree courses (20 each).
     * - At least 30 students applied each to TES (35) and TDP (35), leaving 30 unapplied.
     * - Active scholars enrolled for approved applicants.
     * - Welfare cases distributed across the 7 standardized categories.
     */
    public function run(): void
    {
        // 1. Ensure Admin User Exists
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

        // 2. Ensure Academic Year & Semester Exist
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

        // 3. Clean up other scholarships so that ONLY TES and TDP exist
        $allowedScholarshipNames = [
            'Tertiary Education Subsidy (TES)',
            'Tulong Dunong Program (TDP)',
        ];

        $obsoleteScholarships = Scholarship::whereNotIn('name', $allowedScholarshipNames)->get();
        foreach ($obsoleteScholarships as $obs) {
            $obsAppIds = $obs->applications()->pluck('id');
            ApplicationDocument::whereIn('application_id', $obsAppIds)->delete();
            $obs->applications()->delete();
            $obs->scholars()->delete();
            $obs->requirements()->delete();
            WelfareCaseReferral::where('scholarship_id', $obs->id)->update(['scholarship_id' => null]);
            $obs->delete();
        }

        // 4. Clean up legacy student accounts to ensure exactly 100 students exist
        $allowedStudentEmails = [];
        for ($n = 1; $n <= 100; $n++) {
            $allowedStudentEmails[] = "student{$n}@csu.edu.ph";
        }

        $legacyUsers = User::where('role', 'student')->whereNotIn('email', $allowedStudentEmails)->get();
        foreach ($legacyUsers as $lu) {
            if ($lu->student) {
                $st = $lu->student;
                $appIds = $st->applications()->pluck('id');
                ApplicationDocument::whereIn('application_id', $appIds)->delete();
                $st->applications()->delete();
                $st->scholars()->delete();

                $wCaseIds = $st->welfareCases()->pluck('id');
                WelfareCaseReferral::whereIn('welfare_case_id', $wCaseIds)->delete();
                WelfareCaseDocument::whereIn('welfare_case_id', $wCaseIds)->delete();
                $st->welfareCases()->delete();

                $st->delete();
            }
            $lu->delete();
        }

        // 5. Seed Scholarships: TES and TDP Only
        // A. Tertiary Education Subsidy (TES)
        $scholarshipTES = Scholarship::updateOrCreate(
            ['name' => 'Tertiary Education Subsidy (TES)'],
            [
                'academic_year_id' => $ay->id,
                'semester_id' => $sem->id,
                'school_year' => '2026-2027',
                'provider' => 'Commission on Higher Education (CHED)',
                'description' => 'Tertiary Education Subsidy (TES) under RA 10931 (UniFAST) providing financial grants to qualified Filipino undergraduate college students.',
                'benefits' => 'PHP 20,000 allowance per academic year (PHP 10,000 per semester)',
                'available_slots' => 50,
                'min_gwa' => 2.50,
                'max_household_income' => 40000.00,
                'is_mutually_exclusive' => false,
                'application_start_date' => '2026-08-01',
                'application_deadline' => '2026-11-30',
                'coverage_type' => 'semester',
                'status' => 'open',
                'created_by' => $admin->id,
            ]
        );

        // B. Tulong Dunong Program (TDP)
        $scholarshipTDP = Scholarship::updateOrCreate(
            ['name' => 'Tulong Dunong Program (TDP)'],
            [
                'academic_year_id' => $ay->id,
                'semester_id' => $sem->id,
                'school_year' => '2026-2027',
                'provider' => 'Commission on Higher Education (CHED)',
                'description' => 'Financial assistance grant for qualified college students residing in Cagayan Valley under the CHED Tulong Dunong Program.',
                'benefits' => 'PHP 7,500 stipend per semester',
                'available_slots' => 50,
                'min_gwa' => 2.75,
                'max_household_income' => 40000.00,
                'is_mutually_exclusive' => false,
                'application_start_date' => '2026-08-01',
                'application_deadline' => '2026-11-30',
                'coverage_type' => 'semester',
                'status' => 'open',
                'created_by' => $admin->id,
            ]
        );

        // 5. Seed Official Requirements for TES and TDP
        $tesRequirementsList = [
            ['name' => 'Certificate of Registration (COR) / Assessment Form', 'type' => 'document', 'doc_type' => 'COR'],
            ['name' => 'Certificate of Grades (COG) with GWA', 'type' => 'document', 'doc_type' => 'COG'],
            ['name' => 'Certificate of Indigency / 4Ps Household Certification', 'type' => 'document', 'doc_type' => 'Indigency'],
            ['name' => 'Valid Student Identification Card (ID)', 'type' => 'document', 'doc_type' => 'ID'],
        ];

        $tesReqModels = [];
        foreach ($tesRequirementsList as $req) {
            $tesReqModels[$req['doc_type']] = ScholarshipRequirement::updateOrCreate(
                [
                    'scholarship_id' => $scholarshipTES->id,
                    'requirement_name' => $req['name'],
                ],
                [
                    'requirement_type' => $req['type'],
                    'instructions' => 'Please upload a clear PDF, PNG, or JPG copy of your ' . $req['name'] . '.',
                    'deadline' => '2026-11-30',
                    'status' => 'active',
                    'is_required' => true,
                ]
            );
        }

        $tdpRequirementsList = [
            ['name' => 'Certificate of Registration (COR) / Official Receipt', 'type' => 'document', 'doc_type' => 'COR'],
            ['name' => 'Certified True Copy of Grades (COG)', 'type' => 'document', 'doc_type' => 'COG'],
            ['name' => 'Proof of Income / BIR Tax Exemption / Indigency Certificate', 'type' => 'document', 'doc_type' => 'Income'],
            ['name' => 'Valid School ID / Government-issued ID', 'type' => 'document', 'doc_type' => 'ID'],
        ];

        $tdpReqModels = [];
        foreach ($tdpRequirementsList as $req) {
            $tdpReqModels[$req['doc_type']] = ScholarshipRequirement::updateOrCreate(
                [
                    'scholarship_id' => $scholarshipTDP->id,
                    'requirement_name' => $req['name'],
                ],
                [
                    'requirement_type' => $req['type'],
                    'instructions' => 'Please upload a clear PDF, PNG, or JPG copy of your ' . $req['name'] . '.',
                    'deadline' => '2026-11-30',
                    'status' => 'active',
                    'is_required' => true,
                ]
            );
        }

        // 6. Define Authentic Filipino Names & Localities in Cagayan
        $firstNamesMale = [
            'Juan', 'Carlo', 'Angelo', 'Mark', 'Christian', 'Gabriel', 'Joshua', 'Daniel', 'Kenneth', 'John Paul',
            'Kevin', 'Francis', 'Dominic', 'Justin', 'Rafael', 'Miguel', 'Jeric', 'Ethan', 'Vince', 'Adrian',
            'Paolo', 'Ronnel', 'Bryan', 'Neil', 'Arvin', 'Patrick', 'Kyle', 'Renz', 'Darwin', 'Andrei',
            'Jericho', 'Alden', 'Renzo', 'Marvin', 'Gerald', 'Rodel', 'Edgar', 'Dexter', 'Ian', 'Nathaniel',
            'Noel', 'Joel', 'Rommel', 'Dante', 'Lester', 'Rico', 'Anthony', 'Victor', 'Leo', 'Eduardo'
        ];

        $firstNamesFemale = [
            'Maria', 'Kristina', 'Bea', 'Nicole', 'Alyssa', 'Angelica', 'Danica', 'Camille', 'Joyce', 'Patricia',
            'Princess', 'Stephanie', 'Mary Grace', 'Rachelle', 'Christine', 'Kimberly', 'Jasmine', 'Sophia', 'Janelle', 'Erika',
            'Andrea', 'Trisha', 'Clarisse', 'Hannah', 'Charlene', 'Angela', 'Samantha', 'Nadine', 'Bianca', 'Rochelle',
            'Rowena', 'Maricar', 'Giselle', 'Lourdes', 'Kaye', 'Lovely', 'Judy Ann', 'Aileen', 'Melanie', 'Bernadette',
            'Karen', 'Charmaine', 'Arlene', 'Marites', 'Jessa', 'Shiela', 'Donna', 'Lorraine', 'Rosemarie', 'Glenda'
        ];

        $middleNames = [
            'Santos', 'Reyes', 'Cruz', 'Bautista', 'Ocampo', 'Garcia', 'Mendoza', 'Torres', 'Tomas', 'Pascual',
            'Flores', 'Castillo', 'Rivera', 'Morales', 'Aquino', 'Ramos', 'Guzman', 'Soriano', 'Villanueva', 'Cortez',
            'Tolentino', 'Alonzo', 'Fernandez', 'Valenzuela', 'Corpuz', 'Navarro', 'Agcaoili', 'Macababbad', 'Balisi', 'Pimentel'
        ];

        $lastNames = [
            'Dela Cruz', 'Santos', 'Aquino', 'Garcia', 'Reyes', 'Ramos', 'Mendoza', 'Navarro', 'Castro', 'Villanueva',
            'Pascual', 'Bautista', 'Flores', 'Morales', 'Fernandez', 'Tolentino', 'Rivera', 'Salazar', 'Soriano', 'Corpuz',
            'Valenzuela', 'Manalo', 'Guzman', 'Cortez', 'Alonzo', 'Macababbad', 'Pimentel', 'Balisi', 'Cabotaje', 'Agcaoili',
            'Umayam', 'Siriban', 'Collado', 'Tamaray', 'Pagurayan', 'Bunagan', 'Calimag', 'Allam', 'Mallillin', 'Adduru',
            'Baccay', 'Laggui', 'Quilang', 'Caranguian', 'Taguinod', 'Gammad', 'Cauilan', 'Mabanglo', 'Sibal', 'Gannaban'
        ];

        $municipalities = [
            'Lal-lo' => ['Centro', 'Bagumbayan', 'Alucao', 'Catugan', 'Maxingal', 'Cullit', 'Dagupan', 'San Lorenzo', 'Tucalana'],
            'Gattaran' => ['Centro Norte', 'Centro Sur', 'Nassiping', 'Guising', 'Barangay I', 'Barangay II', 'Palagao'],
            'Aparri' => ['Toran', 'Punta', 'San Antonio', 'Maura', 'Macanaya', 'Minanga', 'Plaza'],
            'Buguey' => ['Centro', 'Villa Leonora', 'Quinawegan', 'Tabbac', 'Mala Este', 'San Isidro'],
            'Camalaniugan' => ['Centro Norte', 'Centro Sur', 'Sapping', 'Dugo', 'Catotoran Norte'],
            'Lasam' => ['Centro I', 'Centro II', 'Callao Norte', 'San Nicolas', 'Ignacio Jurado'],
            'Allacapan' => ['Centro West', 'Centro East', 'Daan-Ili', 'Utan', 'Silagan'],
            'Gonzaga' => ['Paradise', 'Caroan', 'Ipil', 'Progressive', 'Smart', 'Amunitan'],
            'Santa Ana' => ['Centro', 'Casambalangan', 'San Vicente', 'Palawig', 'Tangatan'],
            'Ballesteros' => ['Centro West', 'Centro East', 'Cabaritan East', 'Poblacion', 'Ammubuan']
        ];
        $muniKeys = array_keys($municipalities);

        // 7. The 5 Available Degree Courses (100 students / 5 = exactly 20 students per course)
        $coursesData = [
            [
                'course' => 'Bachelor of Science in Information Technology (BSIT)',
                'college' => College::CICS,
            ],
            [
                'course' => 'Bachelor of Science in Agriculture (BSA)',
                'college' => College::COA,
            ],
            [
                'course' => 'Bachelor of Science in Hospitality Management (BSHM)',
                'college' => College::CHM,
            ],
            [
                'course' => 'Bachelor of Elementary Education (BEEd)',
                'college' => College::CTED,
            ],
            [
                'course' => 'Bachelor of Secondary Education (BSEd)',
                'college' => College::CTED,
            ],
        ];

        $passwordHash = Hash::make('password123');
        $allCreatedStudents = [];
        $welfareCasesCount = 0;

        // 8. Iterate Through the 5 Courses to Seed Exactly 100 Students
        foreach ($coursesData as $cIndex => $cData) {
            $courseName = $cData['course'];
            $collegeEnum = $cData['college'];

            for ($i = 0; $i < 20; $i++) {
                $studentNum = ($cIndex * 20) + $i + 1; // 1 to 100
                $isMale = ($studentNum % 2 === 1);

                $firstName = $isMale 
                    ? $firstNamesMale[($studentNum - 1) % count($firstNamesMale)] 
                    : $firstNamesFemale[($studentNum - 1) % count($firstNamesFemale)];
                $middleName = $middleNames[($studentNum * 3) % count($middleNames)];
                $lastName = $lastNames[($studentNum * 7) % count($lastNames)];

                // Year Level Distribution: 5 of each level per course
                if ($i < 5) {
                    $yearLevel = '1st Year';
                } elseif ($i < 10) {
                    $yearLevel = '2nd Year';
                } elseif ($i < 15) {
                    $yearLevel = '3rd Year';
                } else {
                    $yearLevel = '4th Year';
                }

                $studentIdFormatted = '26-' . str_pad($studentNum, 5, '0', STR_PAD_LEFT);
                $contactNum = '0917' . str_pad(1200000 + $studentNum, 7, '0', STR_PAD_LEFT);
                $gwa = round(1.20 + (($studentNum * 17) % 125) / 100, 2);
                $monthlyIncome = round(6000 + (($studentNum * 350) % 22000), 2);
                $is4ps = ($monthlyIncome <= 12000.00);

                $muni = $muniKeys[($studentNum) % count($muniKeys)];
                $brgys = $municipalities[$muni];
                $brgy = $brgys[($studentNum * 2) % count($brgys)];

                $email = "student{$studentNum}@csu.edu.ph";

                // A. Create/Update User Account
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'password' => $passwordHash,
                        'role' => 'student',
                    ]
                );

                // B. Create/Update Student Profile
                $student = Student::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'student_number' => $studentIdFormatted,
                        'sex' => $isMale ? 'Male' : 'Female',
                        'college' => $collegeEnum,
                        'course' => $courseName,
                        'program' => $courseName,
                        'year_level' => $yearLevel,
                        'contact_number' => $contactNum,
                        'current_gwa' => $gwa,
                        'monthly_household_income' => $monthlyIncome,
                        'municipality' => $muni,
                        'barangay' => $brgy,
                        'is_4ps' => $is4ps,
                    ]
                );

                $allCreatedStudents[] = $student;

                // C. Seed Scholarship Applications:
                // - Students $i 0..6 (7 students * 5 courses = 35 students) -> Applied to TES
                // - Students $i 7..13 (7 students * 5 courses = 35 students) -> Applied to TDP
                // - Students $i 14..19 (6 students * 5 courses = 30 students) -> Unapplied (Fresh)
                if ($i < 7) {
                    // TES Applicant
                    $targetScholarship = $scholarshipTES;
                    $reqSet = $tesReqModels;

                    if ($i < 4) {
                        // 4 approved per course (20 total approved TES scholars)
                        $appStatus = 'approved';
                        $remarks = 'Passed qualification screening and academic requirement threshold.';
                        $createScholar = true;
                    } elseif ($i === 4) {
                        // 1 submitted per course (5 total)
                        $appStatus = 'submitted';
                        $remarks = 'Application submitted successfully; pending document assessment.';
                        $createScholar = false;
                    } elseif ($i === 5) {
                        // 1 under_review per course (5 total)
                        $appStatus = 'under_review';
                        $remarks = 'Documents under formal review by OSDW scholarship evaluators.';
                        $createScholar = false;
                    } else {
                        // 1 deficient per course (5 total)
                        $appStatus = 'deficient';
                        $remarks = 'Please upload an official and readable Certificate of Grades (COG).';
                        $createScholar = false;
                    }

                    $application = Application::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'scholarship_id' => $targetScholarship->id,
                        ],
                        [
                            'student_gwa' => $gwa,
                            'monthly_income' => $monthlyIncome,
                            'status' => $appStatus,
                            'remarks' => $remarks,
                            'submitted_at' => now()->subDays(15 - ($studentNum % 10)),
                        ]
                    );

                    // Attach Application Documents (generate realistic physical PDF files)
                    foreach ($reqSet as $docKey => $reqModel) {
                        $docStatus = 'verified';
                        $docRemarks = null;
                        $verifiedAt = now()->subDays(10);

                        if ($appStatus === 'submitted') {
                            $docStatus = 'pending';
                            $verifiedAt = null;
                        } elseif ($appStatus === 'under_review') {
                            if ($docKey === 'ID') {
                                $docStatus = 'pending';
                                $verifiedAt = null;
                            }
                        } elseif ($appStatus === 'deficient') {
                            if ($docKey === 'COG') {
                                $docStatus = 'needs_resubmission';
                                $docRemarks = 'Scanned copy of Certificate of Grades was truncated and unreadable.';
                                $verifiedAt = null;
                            }
                        }

                        $fileSuffix = strtolower($docKey);
                        $filePath = "documents/applications/{$student->student_number}_{$fileSuffix}.pdf";
                        $origFilename = "{$docKey}_{$student->student_number}.pdf";

                        $pdfContent = match ($docKey) {
                            'COR' => SampleDocumentGenerator::generateCOR($student, $targetScholarship->name),
                            'COG' => SampleDocumentGenerator::generateCOG($student, $targetScholarship->name),
                            'Indigency' => SampleDocumentGenerator::generateIndigency($student),
                            'ID' => SampleDocumentGenerator::generateStudentID($student),
                            default => SampleDocumentGenerator::generateCOR($student, $targetScholarship->name),
                        };

                        Storage::disk('local')->put($filePath, $pdfContent);

                        ApplicationDocument::updateOrCreate(
                            [
                                'application_id' => $application->id,
                                'scholarship_requirement_id' => $reqModel->id,
                            ],
                            [
                                'file_path' => $filePath,
                                'original_filename' => $origFilename,
                                'status' => $docStatus,
                                'remarks' => $docRemarks,
                                'verified_at' => $verifiedAt,
                            ]
                        );
                    }

                    // Enroll Active Scholar if Approved
                    if ($createScholar) {
                        Scholar::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'scholarship_id' => $targetScholarship->id,
                            ],
                            [
                                'application_id' => $application->id,
                                'status' => 'active',
                                'approved_at' => now()->subDays(8),
                            ]
                        );
                    }

                } elseif ($i >= 7 && $i < 14) {
                    // TDP Applicant
                    $targetScholarship = $scholarshipTDP;
                    $reqSet = $tdpReqModels;

                    if ($i < 11) {
                        // 4 approved per course (20 total approved TDP scholars)
                        $appStatus = 'approved';
                        $remarks = 'Passed qualification screening under CHED Tulong Dunong grant allocation.';
                        $createScholar = true;
                    } elseif ($i === 11) {
                        // 1 submitted per course (5 total)
                        $appStatus = 'submitted';
                        $remarks = 'Application documents received; undergoing preliminary verification.';
                        $createScholar = false;
                    } elseif ($i === 12) {
                        // 1 under_review per course (5 total)
                        $appStatus = 'under_review';
                        $remarks = 'Endorsed for final eligibility verification by CHED TDP regional focal person.';
                        $createScholar = false;
                    } else {
                        // 1 rejected per course (5 total)
                        $appStatus = 'rejected';
                        $remarks = 'Household monthly income exceeds maximum allowable threshold for Tulong Dunong grant.';
                        $createScholar = false;
                    }

                    $application = Application::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'scholarship_id' => $targetScholarship->id,
                        ],
                        [
                            'student_gwa' => $gwa,
                            'monthly_income' => $monthlyIncome,
                            'status' => $appStatus,
                            'remarks' => $remarks,
                            'submitted_at' => now()->subDays(12 - ($studentNum % 8)),
                        ]
                    );

                    // Attach Application Documents (generate realistic physical PDF files)
                    foreach ($reqSet as $docKey => $reqModel) {
                        $docStatus = 'verified';
                        $docRemarks = null;
                        $verifiedAt = now()->subDays(7);

                        if ($appStatus === 'submitted') {
                            $docStatus = 'pending';
                            $verifiedAt = null;
                        } elseif ($appStatus === 'under_review') {
                            if ($docKey === 'Income') {
                                $docStatus = 'pending';
                                $verifiedAt = null;
                            }
                        }

                        $fileSuffix = strtolower($docKey);
                        $filePath = "documents/applications/{$student->student_number}_{$fileSuffix}.pdf";
                        $origFilename = "{$docKey}_{$student->student_number}.pdf";

                        $pdfContent = match ($docKey) {
                            'COR' => SampleDocumentGenerator::generateCOR($student, $targetScholarship->name),
                            'COG' => SampleDocumentGenerator::generateCOG($student, $targetScholarship->name),
                            'Income' => SampleDocumentGenerator::generateIndigency($student),
                            'ID' => SampleDocumentGenerator::generateStudentID($student),
                            default => SampleDocumentGenerator::generateCOR($student, $targetScholarship->name),
                        };

                        Storage::disk('local')->put($filePath, $pdfContent);

                        ApplicationDocument::updateOrCreate(
                            [
                                'application_id' => $application->id,
                                'scholarship_requirement_id' => $reqModel->id,
                            ],
                            [
                                'file_path' => $filePath,
                                'original_filename' => $origFilename,
                                'status' => $docStatus,
                                'remarks' => $docRemarks,
                                'verified_at' => $verifiedAt,
                            ]
                        );
                    }

                    // Enroll Active Scholar if Approved
                    if ($createScholar) {
                        Scholar::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'scholarship_id' => $targetScholarship->id,
                            ],
                            [
                                'application_id' => $application->id,
                                'status' => 'active',
                                'approved_at' => now()->subDays(6),
                            ]
                        );
                    }
                }
                // Else ($i >= 14): unapplied students (6 per course = 30 total unapplied)
            }
        }

        // 9. Seed Realistic Student Welfare Cases (21 students across 7 standardized categories)
        $welfareCasesConfig = [
            [
                'category' => WelfareCase::CATEGORY_SCHOLARSHIP,
                'desc' => 'Requested financial support for boarding house expenses, connectivity, and academic project requirements.',
                'status' => 'Resolved',
                'refer_type' => 'scholarship',
                'refer_to' => null,
                'scholarship' => $scholarshipTES,
            ],
            [
                'category' => WelfareCase::CATEGORY_ASSISTANTSHIP,
                'desc' => 'Applied for Student Assistant (SA) duties at the campus library and university computer laboratories.',
                'status' => 'Open',
                'refer_type' => 'office',
                'refer_to' => 'OSDW Student Assistantship In-Charge',
                'scholarship' => null,
            ],
            [
                'category' => WelfareCase::CATEGORY_SMAP,
                'desc' => 'Mutual financial aid emergency request following hospitalization and emergency medical expenses of family breadwinner.',
                'status' => 'For Follow-up',
                'refer_type' => 'office',
                'refer_to' => 'OSDW Student Mutual Aid Program (SMAP) Unit',
                'scholarship' => null,
            ],
            [
                'category' => WelfareCase::CATEGORY_CO_CURRICULAR,
                'desc' => 'Application for regional inter-collegiate robotics competition achievement award and co-curricular incentive grant.',
                'status' => 'Resolved',
                'refer_type' => 'office',
                'refer_to' => 'OSDW Student Development Unit',
                'scholarship' => null,
            ],
            [
                'category' => WelfareCase::CATEGORY_TOPNOTCHERS,
                'desc' => 'Submission of university recognition documentation for licensure examination topnotcher incentive grant program.',
                'status' => 'Under Assessment',
                'refer_type' => 'office',
                'refer_to' => 'OSDW Honors and Awards Committee',
                'scholarship' => null,
            ],
            [
                'category' => WelfareCase::CATEGORY_ORG_ACCREDITATION,
                'desc' => 'Submitted official accreditation dossier, Constitution & By-Laws, and officer roster for campus student organization.',
                'status' => 'Resolved',
                'refer_type' => 'office',
                'refer_to' => 'OSDW Student Organizations & Activities Unit (SOAU)',
                'scholarship' => null,
            ],
            [
                'category' => WelfareCase::CATEGORY_ORG_ADVISING,
                'desc' => 'Requested official student leadership training advising, campus event permits, and university faculty adviser liaison.',
                'status' => 'Open',
                'refer_type' => 'office',
                'refer_to' => 'OSDW Student Organizations & Activities Unit (SOAU)',
                'scholarship' => null,
            ],
        ];

        // Seed 3 cases per category (total 21 cases) across students 70 to 90
        $wCaseIdx = 0;
        foreach ($welfareCasesConfig as $cfg) {
            for ($rep = 0; $rep < 3; $rep++) {
                $targetStudent = $allCreatedStudents[70 + $wCaseIdx] ?? $allCreatedStudents[$wCaseIdx];
                $caseControlId = 'WC-2026-' . str_pad($wCaseIdx + 1, 4, '0', STR_PAD_LEFT);

                $welfareCase = WelfareCase::updateOrCreate(
                    ['case_id' => $caseControlId],
                    [
                        'student_id' => $targetStudent->id,
                        'category' => $cfg['category'],
                        'description' => $cfg['desc'],
                        'requested_information' => 'Complete intake assessment and endorse to appropriate OSDW unit.',
                        'status' => $cfg['status'],
                        'created_at' => now()->subDays(25 - $wCaseIdx),
                        'updated_at' => in_array($cfg['status'], ['Resolved', 'Closed']) ? now()->subDays(2) : now(),
                    ]
                );

                if ($cfg['refer_type'] === 'scholarship' && $cfg['scholarship']) {
                    WelfareCaseReferral::updateOrCreate(
                        [
                            'welfare_case_id' => $welfareCase->id,
                            'referral_type' => 'scholarship',
                        ],
                        [
                            'scholarship_id' => $cfg['scholarship']->id,
                            'referred_to_office' => null,
                            'referral_note' => 'Officially evaluated and endorsed for financial grant support under ' . $cfg['scholarship']->name,
                        ]
                    );
                } elseif ($cfg['refer_to']) {
                    WelfareCaseReferral::updateOrCreate(
                        [
                            'welfare_case_id' => $welfareCase->id,
                            'referred_to_office' => $cfg['refer_to'],
                        ],
                        [
                            'scholarship_id' => null,
                            'referral_type' => 'office',
                            'referral_note' => 'Referred for specialized assistance and intervention to ' . $cfg['refer_to'],
                        ]
                    );
                }

                $welfareFilePath = "welfare_cases/{$caseControlId}_intake.pdf";
                $welfareFileName = "Official_Case_Intake_Assessment_{$targetStudent->student_number}.pdf";

                $welfarePdf = SampleDocumentGenerator::generateWelfareIntake($welfareCase, $targetStudent);
                Storage::disk('local')->put($welfareFilePath, $welfarePdf);

                WelfareCaseDocument::updateOrCreate(
                    [
                        'welfare_case_id' => $welfareCase->id,
                        'file_name' => $welfareFileName,
                    ],
                    [
                        'file_path' => $welfareFilePath,
                    ]
                );

                $wCaseIdx++;
            }
        }
    }
}
