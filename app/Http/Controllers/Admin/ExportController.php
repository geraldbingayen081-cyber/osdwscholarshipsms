<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Scholar;
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

        $query = Scholar::with(['student.user', 'scholarship.academicYear']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($scholarshipId) {
            $query->where('scholarship_id', $scholarshipId);
        }

        $scholars = $query->latest('approved_at')->get();

        $filename = 'csu_lallo_active_scholars_' . date('Y_m_d_His') . '.csv';

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
                'Scholar ID',
                'Student ID',
                'Student Name',
                'Email',
                'Contact Number',
                'Course',
                'Year Level',
                'Scholarship Program',
                'Provider',
                'Coverage Type',
                'School Year',
                'Approved Date',
                'Standing Status',
            ]);

            foreach ($scholars as $scholar) {
                fputcsv($file, [
                    $scholar->id,
                    $scholar->student->student_number ?? 'N/A',
                    $scholar->student->user->full_name ?? 'N/A',
                    $scholar->student->user->email ?? 'N/A',
                    $scholar->student->contact_number ?? 'N/A',
                    $scholar->student->course ?? 'N/A',
                    $scholar->student->year_level ?? 'N/A',
                    $scholar->scholarship->name ?? 'N/A',
                    $scholar->scholarship->provider ?? 'N/A',
                    $scholar->scholarship->coverage_type_label ?? 'N/A',
                    $scholar->scholarship->school_year_label ?? 'N/A',
                    $scholar->approved_at ? $scholar->approved_at->format('Y-m-d') : 'N/A',
                    ucfirst(str_replace('_', ' ', $scholar->status)),
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

        $query = Application::with(['student.user', 'scholarship.academicYear']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($academicYearId) {
            $query->whereHas('scholarship', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        $applications = $query->latest('submitted_at')->get();

        $filename = 'csu_lallo_student_applications_' . date('Y_m_d_His') . '.csv';

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
                'Application ID',
                'Student ID',
                'Student Name',
                'Email',
                'Course',
                'Year Level',
                'Scholarship Program',
                'Coverage Type',
                'School Year',
                'Submitted Date',
                'Application Status',
                'Admin Remarks',
            ]);

            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->student->student_number ?? 'N/A',
                    $app->student->user->full_name ?? 'N/A',
                    $app->student->user->email ?? 'N/A',
                    $app->student->course ?? 'N/A',
                    $app->student->year_level ?? 'N/A',
                    $app->scholarship->name ?? 'N/A',
                    $app->scholarship->coverage_type_label ?? 'N/A',
                    $app->scholarship->school_year_label ?? 'N/A',
                    $app->submitted_at ? $app->submitted_at->format('Y-m-d H:i:s') : 'N/A',
                    ucfirst(str_replace('_', ' ', $app->status)),
                    $app->remarks ?? 'None',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
