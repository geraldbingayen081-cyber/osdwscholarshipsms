<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\ScholarCompliance;
use App\Models\WelfareCase;
use App\Models\WelfareCaseReferral;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Download Active Scholars roster as a formatted CSV file.
     */
    public function exportScholars(Request $request)
    {
        $status = $request->get('status');
        $scholarshipId = $request->get('scholarship_id');
        $academicYearId = $request->get('academic_year_id');
        $college = $request->get('college');

        $query = Scholar::with(['student.user', 'scholarship.academicYear']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        if ($academicYearId) {
            $query->whereHas('scholarship', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        if ($college) {
            $query->whereHas('student', function ($q) use ($college) {
                $q->where('college', $college);
            });
        }

        $scholars = $query->latest('approved_at')->get();

        \App\Models\SystemLog::record(
            'Export',
            'export_csv',
            "Exported " . $scholars->count() . " Active Scholar record(s) to CSV.",
            null,
            ['status' => $status, 'scholarship_id' => $scholarshipId, 'count' => $scholars->count()]
        );

        $filename = 'csu_lallo_scholars_masterlist_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($scholars) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No.',
                'Student ID',
                'Full Name',
                'Sex',
                'Course',
                'Scholarship',
            ]);

            $i = 1;
            foreach ($scholars as $scholar) {
                fputcsv($file, [
                    $i++,
                    $scholar->student->student_number ?? 'N/A',
                    $scholar->student->user->full_name ?? 'N/A',
                    $scholar->student->sex ?? 'N/A',
                    $scholar->student->course ?? 'N/A',
                    $scholar->scholarship->name ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Student Applications report as a formatted CSV file.
     */
    public function exportApplications(Request $request)
    {
        $status = $request->get('status');
        $academicYearId = $request->get('academic_year_id');
        $scholarshipId = $request->get('scholarship_id');
        $college = $request->get('college');

        $query = Application::with(['student.user', 'scholarship.academicYear']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        if ($academicYearId) {
            $query->whereHas('scholarship', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        if ($college) {
            $query->whereHas('student', function ($q) use ($college) {
                $q->where('college', $college);
            });
        }

        $applications = $query->latest('submitted_at')->get();

        \App\Models\SystemLog::record(
            'Export',
            'export_csv',
            "Exported " . $applications->count() . " Student Application record(s) to CSV.",
            null,
            ['status' => $status, 'academic_year_id' => $academicYearId, 'count' => $applications->count()]
        );

        $filename = 'csu_lallo_applications_summary_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No.',
                'App. Ref #',
                'Student ID',
                'Applicant Name',
                'Sex',
                'Course',
                'Target Scholarship',
                'GWA',
                'Monthly Income',
                '4Ps Beneficiary',
                'Date Filed',
                'Application Status',
                'Admin Remarks',
            ]);

            $i = 1;
            foreach ($applications as $app) {
                fputcsv($file, [
                    $i++,
                    $app->id,
                    $app->student->student_number ?? 'N/A',
                    $app->student->user->full_name ?? 'N/A',
                    $app->student->sex ?? 'N/A',
                    $app->student->course ?? 'N/A',
                    $app->scholarship->name ?? 'N/A',
                    $app->student->current_gwa ?? 'N/A',
                    $app->student->monthly_household_income ? 'PHP ' . number_format($app->student->monthly_household_income, 2) : 'N/A',
                    $app->student->is_4ps ? 'Yes' : 'No',
                    $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i:s') : 'N/A',
                    ucfirst(str_replace('_', ' ', $app->status)),
                    $app->remarks ?? 'None',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Program Slot Utilization report as CSV.
     */
    public function exportSlotUtilization(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');

        $query = Scholarship::withCount(['applications', 'scholars']);
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        $scholarships = $query->orderBy('name')->get();

        $filename = 'csu_lallo_slot_utilization_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($scholarships) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No.',
                'Scholarship Program',
                'Provider / Sponsor',
                'Coverage Type',
                'Allocated Slots',
                'Total Applicants',
                'Enrolled Scholars',
                'Remaining Slots',
                'Utilization Rate (%)',
                'Program Status',
            ]);

            $i = 1;
            foreach ($scholarships as $sch) {
                $rem = max(0, $sch->available_slots - $sch->scholars_count);
                $rate = $sch->available_slots > 0 ? round(($sch->scholars_count / $sch->available_slots) * 100, 1) : 0;

                fputcsv($file, [
                    $i++,
                    $sch->name,
                    $sch->provider ?? 'N/A',
                    $sch->coverage_type_label ?? 'N/A',
                    $sch->available_slots,
                    $sch->applications_count,
                    $sch->scholars_count,
                    $rem,
                    $rate . '%',
                    ucfirst($sch->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Scholar Compliance Tracking report as CSV.
     */
    public function exportCompliance(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $scholarshipId = $request->get('scholarship_id');
        $status = $request->get('status');

        $query = ScholarCompliance::with(['scholar.student.user', 'scholar.scholarship', 'complianceRequest.requirements']);

        if ($scholarshipId) {
            $query->whereHas('scholar', function ($q) use ($scholarshipId) {
                $q->where('scholarship_id', $scholarshipId);
            });
        }

        if ($academicYearId) {
            $query->whereHas('complianceRequest', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $compliances = $query->latest('updated_at')->get();

        $filename = 'csu_lallo_compliance_tracking_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($compliances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No.',
                'Student ID',
                'Scholar Name',
                'Sex',
                'Course',
                'Scholarship',
                'Compliance Request',
                'Date Submitted',
                'Submitted GWA',
                'Verification Status',
            ]);

            $i = 1;
            foreach ($compliances as $c) {
                fputcsv($file, [
                    $i++,
                    $c->scholar->student->student_number ?? 'N/A',
                    $c->scholar->student->user->full_name ?? 'N/A',
                    $c->scholar->student->sex ?? 'N/A',
                    $c->scholar->student->course ?? 'N/A',
                    $c->scholar->scholarship->name ?? 'N/A',
                    $c->complianceRequest->title ?? 'N/A',
                    $c->submitted_at ? $c->submitted_at->format('Y-m-d') : 'N/A',
                    $c->scholar->student->current_gwa ?? 'N/A',
                    $c->status_label,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Student Welfare Cases report as CSV.
     */
    public function exportWelfareCases(Request $request)
    {
        $status = $request->get('status');
        $category = $request->get('category');
        $college = $request->get('college');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = WelfareCase::with(['student.user']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category', $category);
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

        $cases = $query->latest('created_at')->get();

        $filename = 'csu_lallo_welfare_cases_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($cases) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No.',
                'Case ID',
                'Intake Date',
                'Student ID',
                'Student Name',
                'Sex',
                'Course',
                'Case Category',
                'Description',
                'Case Status',
                'Resolution Date',
            ]);

            $i = 1;
            foreach ($cases as $c) {
                fputcsv($file, [
                    $i++,
                    $c->case_id,
                    $c->created_at->format('Y-m-d'),
                    $c->student->student_number ?? 'N/A',
                    $c->student->user->full_name ?? 'N/A',
                    $c->student->sex ?? 'N/A',
                    $c->student->course ?? 'N/A',
                    $c->category,
                    $c->description,
                    $c->status,
                    in_array($c->status, ['Resolved', 'Closed']) ? $c->updated_at->format('Y-m-d') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Student Welfare Referrals report as CSV.
     */
    public function exportWelfareReferrals(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = WelfareCaseReferral::with(['welfareCase.student.user', 'scholarship']);

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $referrals = $query->latest('created_at')->get();

        $filename = 'csu_lallo_welfare_referrals_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($referrals) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'No.',
                'Case ID',
                'Student Name',
                'Sex',
                'Course',
                'Referral Type',
                'Referred To',
                'Referral Date',
                'Endorsement Reason',
            ]);

            $i = 1;
            foreach ($referrals as $ref) {
                $dest = $ref->referral_type === 'scholarship' 
                    ? ($ref->scholarship->name ?? 'Scholarship Program') 
                    : ($ref->referred_to_office ?? 'Campus Office');

                fputcsv($file, [
                    $i++,
                    $ref->welfareCase->case_id ?? 'N/A',
                    $ref->welfareCase->student->user->full_name ?? 'N/A',
                    $ref->welfareCase->student->sex ?? 'N/A',
                    $ref->welfareCase->student->course ?? 'N/A',
                    ucfirst(str_replace('_', ' ', $ref->referral_type)),
                    $dest,
                    $ref->created_at->format('Y-m-d'),
                    $ref->referral_note ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
