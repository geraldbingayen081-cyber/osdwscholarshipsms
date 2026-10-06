<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarCompliance;
use App\Models\Student;
use App\Models\WelfareCase;
use App\Models\WelfareCaseReferral;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display the Centralized Reports Generation Hub.
     */
    public function index(Request $request)
    {
        $reportData = $this->resolveReportData($request);

        return view('admin.reports.index', $reportData);
    }

    /**
     * Display dedicated clean printable report view with CSU letterhead and signature block.
     */
    public function print(Request $request)
    {
        $reportData = $this->resolveReportData($request);

        return view('admin.reports.print', $reportData);
    }

    /**
     * Display dedicated printable 1-page individual Student Welfare Case Dossier.
     */
    public function welfareDossier(WelfareCase $welfareCase)
    {
        $welfareCase->load(['student.user', 'documents', 'referrals']);

        return view('admin.reports.case_dossier', [
            'case' => $welfareCase,
            'institutionName' => SystemSetting::get('institution_name', 'CAGAYAN STATE UNIVERSITY'),
            'campusName' => SystemSetting::get('campus_name', 'LAL-LO CAMPUS'),
            'officeName' => SystemSetting::get('office_name', 'OFFICE OF STUDENT DEVELOPMENT AND WELFARE (OSDW)'),
        ]);
    }

    /**
     * Helper to resolve filters, dynamic queries, report titles, and summary cards.
     */
    public function resolveReportData(Request $request): array
    {
        $tab = $request->get('tab', 'scholarships'); // 'scholarships' | 'welfare'
        $type = $request->get('type');

        if (!$type) {
            $type = $tab === 'welfare' ? 'welfare_masterlist' : 'scholars_masterlist';
        }

        // Shared Filter Parameters
        $academicYearId = $request->get('academic_year_id');
        $scholarshipId = $request->get('scholarship_id');
        $college = $request->get('college');
        $status = $request->get('status');
        $category = $request->get('category');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Dropdown options
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $selectedAY = $academicYearId ? AcademicYear::find($academicYearId) : null;
        $scholarships = Scholarship::orderBy('name')->get();
        $selectedScholarship = $scholarshipId ? Scholarship::find($scholarshipId) : null;

        $colleges = [
            'CICS' => 'College of Information and Computing Sciences (CICS)',
            'COA' => 'College of Agriculture (COA)',
            'CTED' => 'College of Teacher Education (CTED)',
            'CHM' => 'College of Hospitality Management (CHM)',
        ];

        $records = collect();
        $summaryStats = [];
        $reportTitle = 'OFFICIAL REPORT';
        $reportSubtitle = ($selectedAY ? 'Academic Year ' . $selectedAY->name : 'All Academic Years');

        if ($tab === 'scholarships') {
            switch ($type) {
                case 'scholars_masterlist':
                    // Dynamic Title
                    if ($selectedScholarship) {
                        $reportTitle = 'OFFICIAL MASTERLIST OF ' . strtoupper($selectedScholarship->name) . ' SCHOLARS';
                    } else {
                        $reportTitle = 'OFFICIAL MASTERLIST OF SCHOLARS & GRANTEES';
                    }

                    $query = Scholar::with(['student.user', 'scholarship.academicYear']);

                    if ($selectedScholarship) {
                        $query->where('scholarship_id', $selectedScholarship->id);
                    }

                    if ($selectedAY) {
                        $query->whereHas('scholarship', function ($q) use ($selectedAY) {
                            $q->where('academic_year_id', $selectedAY->id);
                        });
                    }

                    if ($college) {
                        $query->whereHas('student', function ($q) use ($college) {
                            $q->where('college', $college);
                        });
                    }

                    if ($status) {
                        $query->where('status', $status);
                    }

                    $records = $query->latest('approved_at')->get();

                    $totalCount = $records->count();
                    $maleCount = $records->filter(fn($r) => strtolower($r->student->sex ?? '') === 'male')->count();
                    $femaleCount = $records->filter(fn($r) => strtolower($r->student->sex ?? '') === 'female')->count();

                    $summaryStats = [
                        ['label' => 'Total Scholars', 'value' => $totalCount],
                        ['label' => 'Male Scholars', 'value' => $maleCount],
                        ['label' => 'Female Scholars', 'value' => $femaleCount],
                    ];
                    break;

                case 'applications_summary':
                    $reportTitle = 'SCHOLARSHIP APPLICATIONS & EVALUATION REPORT';
                    if ($selectedScholarship) {
                        $reportTitle .= ' - ' . strtoupper($selectedScholarship->name);
                    }

                    $query = Application::with(['student.user', 'scholarship.academicYear']);

                    if ($selectedScholarship) {
                        $query->where('scholarship_id', $selectedScholarship->id);
                    }

                    if ($selectedAY) {
                        $query->whereHas('scholarship', function ($q) use ($selectedAY) {
                            $q->where('academic_year_id', $selectedAY->id);
                        });
                    }

                    if ($college) {
                        $query->whereHas('student', function ($q) use ($college) {
                            $q->where('college', $college);
                        });
                    }

                    if ($status) {
                        $query->where('status', $status);
                    }

                    if ($dateFrom) {
                        $query->whereDate('submitted_at', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $query->whereDate('submitted_at', '<=', $dateTo);
                    }

                    $records = $query->latest('submitted_at')->get();

                    $totalApps = $records->count();
                    $approved = $records->where('status', 'approved')->count();
                    $pending = $records->whereIn('status', ['submitted', 'under_review'])->count();
                    $rejected = $records->where('status', 'rejected')->count();

                    $summaryStats = [
                        ['label' => 'Total Applications', 'value' => $totalApps],
                        ['label' => 'Approved', 'value' => $approved],
                        ['label' => 'Pending Review', 'value' => $pending],
                        ['label' => 'Rejected', 'value' => $rejected],
                    ];
                    break;

                case 'slot_utilization':
                    $reportTitle = 'SCHOLARSHIP PROGRAM SLOT UTILIZATION & CAPACITY REPORT';

                    $query = Scholarship::withCount(['applications', 'scholars']);
                    if ($selectedAY) {
                        $query->where('academic_year_id', $selectedAY->id);
                    }
                    $records = $query->orderBy('name')->get();

                    $totalSlots = $records->sum('available_slots');
                    $totalFilled = $records->sum('scholars_count');
                    $overallRate = $totalSlots > 0 ? round(($totalFilled / $totalSlots) * 100, 1) : 0;

                    $summaryStats = [
                        ['label' => 'Total Programs', 'value' => $records->count()],
                        ['label' => 'Allocated Slots', 'value' => $totalSlots],
                        ['label' => 'Enrolled Scholars', 'value' => $totalFilled],
                        ['label' => 'Overall Utilization', 'value' => "{$overallRate}%"],
                    ];
                    break;

                case 'compliance_tracking':
                    $reportTitle = 'SCHOLAR COMPLIANCE & RENEWAL STATUS REPORT';

                    $query = ScholarCompliance::with(['scholar.student.user', 'scholar.scholarship', 'complianceRequest.requirements', 'documents']);

                    if ($selectedScholarship) {
                        $query->whereHas('scholar', function ($q) use ($selectedScholarship) {
                            $q->where('scholarship_id', $selectedScholarship->id);
                        });
                    }

                    if ($selectedAY) {
                        $query->whereHas('complianceRequest', function ($q) use ($selectedAY) {
                            $q->where('academic_year_id', $selectedAY->id);
                        });
                    }

                    if ($status) {
                        $query->where('status', $status);
                    }

                    $records = $query->latest('updated_at')->get();

                    $totalReqs = $records->count();
                    $completed = $records->where('status', 'completed')->count();
                    $underReview = $records->whereIn('status', ['submitted', 'under_review'])->count();
                    $overdue = $records->where('status', 'overdue')->count();

                    $summaryStats = [
                        ['label' => 'Total Compliance Records', 'value' => $totalReqs],
                        ['label' => 'Completed / Verified', 'value' => $completed],
                        ['label' => 'Under Review', 'value' => $underReview],
                        ['label' => 'Overdue', 'value' => $overdue],
                    ];
                    break;
            }
        } else {
            // Welfare Tab
            switch ($type) {
                case 'welfare_masterlist':
                default:
                    $reportTitle = 'STUDENT WELFARE CASES MASTER INTAKE LOG';

                    $query = WelfareCase::with(['student.user']);

                    if ($category) {
                        $query->where('category', $category);
                    }

                    if ($status) {
                        $query->where('status', $status);
                    }

                    if ($college) {
                        $query->whereHas('student', function ($q) use ($college) {
                            $q->where('college', $college);
                        });
                    }

                    if ($dateFrom) {
                        $query->whereDate('created_at', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $query->whereDate('created_at', '<=', $dateTo);
                    }

                    $records = $query->latest('created_at')->get();

                    $totalCases = $records->count();
                    $openCases = $records->whereIn('status', ['Open', 'Under Assessment'])->count();
                    $referredCases = $records->where('status', 'Referred')->count();
                    $resolvedCases = $records->whereIn('status', ['Resolved', 'Closed'])->count();

                    $summaryStats = [
                        ['label' => 'Total Cases', 'value' => $totalCases],
                        ['label' => 'Open / In Assessment', 'value' => $openCases],
                        ['label' => 'Referred Cases', 'value' => $referredCases],
                        ['label' => 'Resolved / Closed', 'value' => $resolvedCases],
                    ];
                    break;

                case 'welfare_referrals':
                    $reportTitle = 'STUDENT WELFARE REFERRALS & ENDORSEMENTS REPORT';

                    $query = WelfareCaseReferral::with(['welfareCase.student.user', 'scholarship']);

                    if ($dateFrom) {
                        $query->whereDate('created_at', '>=', $dateFrom);
                    }
                    if ($dateTo) {
                        $query->whereDate('created_at', '<=', $dateTo);
                    }

                    $records = $query->latest('created_at')->get();

                    $totalReferrals = $records->count();
                    $scholarshipRefs = $records->where('referral_type', 'scholarship')->count();
                    $officeRefs = $records->where('referral_type', '!=', 'scholarship')->count();

                    $summaryStats = [
                        ['label' => 'Total Referrals', 'value' => $totalReferrals],
                        ['label' => 'Scholarship Endorsements', 'value' => $scholarshipRefs],
                        ['label' => 'Office / Counseling Referrals', 'value' => $officeRefs],
                    ];
                    break;
            }
        }

        // Institution Settings for headers
        $institutionName = SystemSetting::get('institution_name', 'CAGAYAN STATE UNIVERSITY');
        $campusName = SystemSetting::get('campus_name', 'LAL-LO CAMPUS');
        $officeName = SystemSetting::get('office_name', 'OFFICE OF STUDENT DEVELOPMENT AND WELFARE (OSDW)');

        return compact(
            'tab',
            'type',
            'academicYears',
            'selectedAY',
            'scholarships',
            'selectedScholarship',
            'colleges',
            'records',
            'summaryStats',
            'reportTitle',
            'reportSubtitle',
            'academicYearId',
            'scholarshipId',
            'college',
            'status',
            'category',
            'dateFrom',
            'dateTo',
            'institutionName',
            'campusName',
            'officeName'
        );
    }
}
