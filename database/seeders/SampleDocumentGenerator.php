<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\WelfareCase;

class SampleDocumentGenerator
{
    /**
     * Build standard valid PDF-1.4 file with vector graphics, colored banners, and typography.
     */
    public static function createPdf(callable $drawer): string
    {
        $stream = '';
        $doc = new class($stream) {
            public string $stream;

            public function __construct(string &$stream)
            {
                $this->stream = &$stream;
            }

            public function rect(float $x, float $y, float $w, float $h, string $strokeColor = '0 0 0', ?string $fillColor = null, float $lineWidth = 1): void
            {
                $this->stream .= sprintf("%.2f w\n", $lineWidth);
                $this->stream .= "{$strokeColor} RG\n";
                if ($fillColor !== null) {
                    $this->stream .= "{$fillColor} rg\n";
                    $this->stream .= sprintf("%.2f %.2f %.2f %.2f re B\n", $x, $y, $w, $h);
                } else {
                    $this->stream .= sprintf("%.2f %.2f %.2f %.2f re S\n", $x, $y, $w, $h);
                }
            }

            public function line(float $x1, float $y1, float $x2, float $y2, string $color = '0 0 0', float $width = 1): void
            {
                $this->stream .= sprintf("%.2f w\n", $width);
                $this->stream .= "{$color} RG\n";
                $this->stream .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x1, $y1, $x2, $y2);
            }

            public function text(string $text, float $x, float $y, string $font = 'F1', float $size = 10, string $color = '0 0 0'): void
            {
                $escaped = strtr($text, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)']);
                $this->stream .= "BT\n";
                $this->stream .= "/{$font} {$size} Tf\n";
                $this->stream .= "{$color} rg\n";
                $this->stream .= sprintf("%.2f %.2f Td\n", $x, $y);
                $this->stream .= "({$escaped}) Tj\n";
                $this->stream .= "ET\n";
            }
        };

        $drawer($doc);

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[3] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R /F3 6 0 R /F4 7 0 R >> >> /Contents 8 0 R >>";
        $objects[4] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>";
        $objects[7] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique >>";
        $objects[8] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";

        $output = "%PDF-1.4\n";
        $offsets = [];
        for ($i = 1; $i <= 8; $i++) {
            $offsets[$i] = strlen($output);
            $output .= "{$i} 0 obj\n" . $objects[$i] . "\nendobj\n";
        }

        $xrefOffset = strlen($output);
        $output .= "xref\n0 9\n0000000000 65535 f \n";
        for ($i = 1; $i <= 8; $i++) {
            $output .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $output .= "trailer\n<< /Size 9 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $output;
    }

    /**
     * Generate Certificate of Registration (COR).
     */
    public static function generateCOR(Student $student, string $scholarshipName): string
    {
        $fullName = strtoupper($student->user->full_name ?? 'STUDENT NAME');
        $studentId = $student->student_number ?? '26-00000';
        $course = $student->course ?? 'Bachelor of Science in Information Technology (BSIT)';
        $yearLevel = $student->year_level ?? '1st Year';
        $college = $student->college?->name() ?? 'Cagayan State University';

        return self::createPdf(function ($pdf) use ($fullName, $studentId, $course, $yearLevel, $college, $scholarshipName) {
            // Elegant double borders
            $pdf->rect(25, 25, 545, 792, '0.42 0.06 0.10', null, 2);
            $pdf->rect(29, 29, 537, 784, '0.85 0.65 0.13', null, 0.75);

            // CSU Maroon Header Banner
            $pdf->rect(30, 742, 535, 68, '0.42 0.06 0.10', '0.42 0.06 0.10', 0);
            $pdf->text("REPUBLIC OF THE PHILIPPINES", 225, 795, 'F1', 8, '1 1 1');
            $pdf->text("CAGAYAN STATE UNIVERSITY", 175, 778, 'F2', 15, '1 1 1');
            $pdf->text("LAL-LO CAMPUS - STA. MARIA, LAL-LO, CAGAYAN", 185, 764, 'F1', 9, '1 0.85 0.2');
            $pdf->text("OFFICE OF THE CAMPUS REGISTRAR", 210, 750, 'F2', 9, '1 1 1');

            // Document Title
            $pdf->text("CERTIFICATE OF MATRICULATION & REGISTRATION (COR)", 105, 715, 'F2', 13, '0.42 0.06 0.10');
            $pdf->text("First Semester, Academic Year 2026-2027", 200, 700, 'F4', 10, '0.35 0.35 0.35');
            $pdf->text("Official Enrolment Assessment Copy for {$scholarshipName}", 135, 687, 'F1', 9, '0.1 0.5 0.2');

            // Student Information Box
            $pdf->rect(40, 595, 515, 80, '0.8 0.8 0.8', '0.96 0.96 0.97', 1);
            $pdf->text("STUDENT IDENTIFICATION & ACADEMIC CLASSIFICATION", 50, 660, 'F2', 9, '0.42 0.06 0.10');
            $pdf->line(50, 654, 545, 654, '0.85 0.85 0.85', 0.5);

            $pdf->text("Student ID: {$studentId}", 50, 638, 'F2', 9, '0.1 0.1 0.1');
            $pdf->text("Name: {$fullName}", 50, 622, 'F2', 10, '0.1 0.1 0.1');
            $pdf->text("Program: {$course}", 50, 606, 'F1', 8.5, '0.2 0.2 0.2');

            $pdf->text("Year Level: {$yearLevel}", 360, 638, 'F1', 9, '0.2 0.2 0.2');
            $pdf->text("Status: OFFICIALLY ENROLLED", 360, 622, 'F2', 9, '0.05 0.55 0.15');
            $pdf->text("College: " . substr($college, 0, 30), 360, 606, 'F1', 8, '0.3 0.3 0.3');

            // Course Schedule Table Header
            $pdf->rect(40, 555, 515, 24, '0.42 0.06 0.10', '0.42 0.06 0.10', 0);
            $pdf->text("SUBJECT CODE", 48, 563, 'F2', 8.5, '1 1 1');
            $pdf->text("DESCRIPTIVE COURSE TITLE", 130, 563, 'F2', 8.5, '1 1 1');
            $pdf->text("UNITS", 360, 563, 'F2', 8.5, '1 1 1');
            $pdf->text("SCHEDULE", 415, 563, 'F2', 8.5, '1 1 1');
            $pdf->text("ROOM", 495, 563, 'F2', 8.5, '1 1 1');

            // Subject Rows
            $subjects = [
                ['IT 311', 'Systems Integration and Architecture', '3.0', 'M-Th 08:00-09:30', 'IT-Lab 1'],
                ['IT 312', 'Information Assurance and Security', '3.0', 'T-F 09:30-11:00', 'IT-Lab 2'],
                ['IT 313', 'Web Systems and Technologies 2', '3.0', 'M-Th 01:00-02:30', 'ComLab 3'],
                ['IT 314', 'Mobile Application Development', '3.0', 'T-F 02:30-04:00', 'IT-Lab 1'],
                ['CS 311', 'Software Engineering & Project Management', '3.0', 'Wed 08:00-11:00', 'Room 204'],
                ['GE 301', 'Ethics and Professional Accountability', '3.0', 'Sat 08:00-11:00', 'AVR 1'],
            ];

            $y = 535;
            foreach ($subjects as $idx => $s) {
                $bg = ($idx % 2 === 1) ? '0.97 0.97 0.98' : null;
                if ($bg) {
                    $pdf->rect(40, $y - 6, 515, 20, '0.9 0.9 0.9', $bg, 0.5);
                } else {
                    $pdf->line(40, $y - 6, 555, $y - 6, '0.9 0.9 0.9', 0.5);
                }
                $pdf->text($s[0], 48, $y, 'F2', 8, '0.2 0.2 0.2');
                $pdf->text($s[1], 130, $y, 'F1', 8, '0.2 0.2 0.2');
                $pdf->text($s[2], 370, $y, 'F1', 8, '0.2 0.2 0.2');
                $pdf->text($s[3], 415, $y, 'F1', 8, '0.2 0.2 0.2');
                $pdf->text($s[4], 495, $y, 'F1', 8, '0.2 0.2 0.2');
                $y -= 22;
            }

            // Financial & Billing Assessment Box
            $pdf->rect(40, 260, 515, 120, '0.8 0.8 0.8', '0.98 0.98 0.98', 1);
            $pdf->text("FINANCIAL MATRICULATION & SUBSIDY ASSESSMENT", 50, 362, 'F2', 9, '0.42 0.06 0.10');
            $pdf->line(50, 356, 545, 356, '0.85 0.85 0.85', 0.5);

            $pdf->text("Total Academic Load Units: 18.00 Units", 50, 340, 'F2', 8.5, '0.2 0.2 0.2');
            $pdf->text("Tuition Fee Assessment (18.00 Units x PHP 250.00):", 50, 325, 'F1', 8.5, '0.3 0.3 0.3');
            $pdf->text("PHP 4,500.00", 280, 325, 'F1', 8.5, '0.3 0.3 0.3');
            $pdf->text("Miscellaneous, Laboratory & Registration Fees:", 50, 310, 'F1', 8.5, '0.3 0.3 0.3');
            $pdf->text("PHP 2,850.00", 280, 310, 'F1', 8.5, '0.3 0.3 0.3');
            $pdf->text("Less: Free Higher Education Act Subsidy (RA 10931):", 50, 295, 'F2', 8.5, '0.05 0.5 0.15');
            $pdf->text("- PHP 7,350.00", 280, 295, 'F2', 8.5, '0.05 0.5 0.15');
            $pdf->line(50, 288, 350, 288, '0.7 0.7 0.7', 1);
            $pdf->text("NET TOTAL MATRICULATION DUE: PHP 0.00 (FULLY SUBSIDIZED)", 50, 275, 'F2', 9, '0.42 0.06 0.10');

            // Certification Seal Box
            $pdf->rect(360, 268, 185, 80, '0.85 0.65 0.13', null, 1);
            $pdf->text("OFFICIAL DRY SEAL MARK", 390, 335, 'F2', 8, '0.7 0.5 0.1');
            $pdf->text("Verified Bona Fide Student", 385, 315, 'F1', 8, '0.4 0.4 0.4');
            $pdf->text("CSU Lal-lo Registrar's Office", 380, 298, 'F1', 8, '0.4 0.4 0.4');
            $pdf->text("Certified True Copy", 400, 280, 'F2', 8, '0.42 0.06 0.10');

            // Signatures
            $pdf->line(50, 100, 230, 100, '0.3 0.3 0.3', 1);
            $pdf->text("{$fullName}", 75, 88, 'F2', 9, '0.2 0.2 0.2');
            $pdf->text("Student Signature over Printed Name", 65, 75, 'F1', 8, '0.4 0.4 0.4');

            $pdf->line(360, 100, 540, 100, '0.3 0.3 0.3', 1);
            $pdf->text("ENGR. ARISTOTLE P. GONZALES", 370, 88, 'F2', 9, '0.42 0.06 0.10');
            $pdf->text("Campus Registrar, CSU Lal-lo Campus", 375, 75, 'F1', 8, '0.4 0.4 0.4');

            // Footer note
            $pdf->text("NOTE: This official COR is electronically generated and verified for the CSU-OSDW Scholarship Portal.", 65, 42, 'F4', 7.5, '0.5 0.5 0.5');
        });
    }

    /**
     * Generate Certificate of Grades (COG).
     */
    public static function generateCOG(Student $student, string $scholarshipName): string
    {
        $fullName = strtoupper($student->user->full_name ?? 'STUDENT NAME');
        $studentId = $student->student_number ?? '26-00000';
        $course = $student->course ?? 'Bachelor of Science in Information Technology (BSIT)';
        $yearLevel = $student->year_level ?? '1st Year';
        $gwa = number_format((float) ($student->current_gwa ?? 1.50), 2);

        return self::createPdf(function ($pdf) use ($fullName, $studentId, $course, $yearLevel, $gwa, $scholarshipName) {
            // Elegant double borders
            $pdf->rect(25, 25, 545, 792, '0.42 0.06 0.10', null, 2);
            $pdf->rect(29, 29, 537, 784, '0.85 0.65 0.13', null, 0.75);

            // Header Banner
            $pdf->rect(30, 742, 535, 68, '0.42 0.06 0.10', '0.42 0.06 0.10', 0);
            $pdf->text("REPUBLIC OF THE PHILIPPINES", 225, 795, 'F1', 8, '1 1 1');
            $pdf->text("CAGAYAN STATE UNIVERSITY", 175, 778, 'F2', 15, '1 1 1');
            $pdf->text("LAL-LO CAMPUS - STA. MARIA, LAL-LO, CAGAYAN", 185, 764, 'F1', 9, '1 0.85 0.2');
            $pdf->text("OFFICE OF THE CAMPUS REGISTRAR & ACADEMIC EVALUATION", 155, 750, 'F2', 9, '1 1 1');

            // Document Title
            $pdf->text("OFFICIAL CERTIFICATE OF GRADES (COG)", 160, 715, 'F2', 13, '0.42 0.06 0.10');
            $pdf->text("Academic Performance Evaluation Record", 200, 700, 'F4', 10, '0.35 0.35 0.35');
            $pdf->text("Evaluation Copy submitted for: {$scholarshipName}", 150, 687, 'F1', 9, '0.1 0.5 0.2');

            // Student Information Box
            $pdf->rect(40, 605, 515, 70, '0.8 0.8 0.8', '0.96 0.96 0.97', 1);
            $pdf->text("ACADEMIC SCHOLASTIC RECORD DETAILS", 50, 660, 'F2', 9, '0.42 0.06 0.10');
            $pdf->line(50, 654, 545, 654, '0.85 0.85 0.85', 0.5);

            $pdf->text("Student ID: {$studentId}", 50, 638, 'F2', 9, '0.1 0.1 0.1');
            $pdf->text("Name: {$fullName}", 50, 624, 'F2', 10, '0.1 0.1 0.1');
            $pdf->text("Program: {$course}", 50, 610, 'F1', 8.5, '0.2 0.2 0.2');

            $pdf->text("Year Level: {$yearLevel}", 360, 638, 'F1', 9, '0.2 0.2 0.2');
            $pdf->text("General Weighted Average: {$gwa}", 360, 624, 'F2', 10, '0.42 0.06 0.10');
            $pdf->text("Academic Standing: GOOD STANDING", 360, 610, 'F2', 8.5, '0.05 0.55 0.15');

            // Grades Table Header
            $pdf->rect(40, 565, 515, 24, '0.42 0.06 0.10', '0.42 0.06 0.10', 0);
            $pdf->text("COURSE CODE", 48, 573, 'F2', 8.5, '1 1 1');
            $pdf->text("COURSE DESCRIPTIVE TITLE", 130, 573, 'F2', 8.5, '1 1 1');
            $pdf->text("UNITS", 360, 573, 'F2', 8.5, '1 1 1');
            $pdf->text("FINAL GRADE", 415, 573, 'F2', 8.5, '1 1 1');
            $pdf->text("REMARKS", 495, 573, 'F2', 8.5, '1 1 1');

            // Grades Breakdown
            $baseGrade = (float) $gwa;
            $g1 = number_format(max(1.00, $baseGrade - 0.20), 2);
            $g2 = number_format(max(1.00, $baseGrade - 0.10), 2);
            $g3 = number_format($baseGrade, 2);
            $g4 = number_format(min(2.75, $baseGrade + 0.10), 2);
            $g5 = number_format(min(2.75, $baseGrade + 0.15), 2);
            $g6 = number_format(min(2.75, $baseGrade + 0.05), 2);

            $rows = [
                ['IT 311', 'Systems Integration and Architecture', '3.0', $g1, 'Passed'],
                ['IT 312', 'Information Assurance and Security', '3.0', $g2, 'Passed'],
                ['IT 313', 'Web Systems and Technologies 2', '3.0', $g3, 'Passed'],
                ['IT 314', 'Mobile Application Development', '3.0', $g4, 'Passed'],
                ['CS 311', 'Software Engineering & Project Management', '3.0', $g5, 'Passed'],
                ['GE 301', 'Ethics and Professional Accountability', '3.0', $g6, 'Passed'],
            ];

            $y = 545;
            foreach ($rows as $idx => $r) {
                $bg = ($idx % 2 === 1) ? '0.97 0.97 0.98' : null;
                if ($bg) {
                    $pdf->rect(40, $y - 6, 515, 20, '0.9 0.9 0.9', $bg, 0.5);
                } else {
                    $pdf->line(40, $y - 6, 555, $y - 6, '0.9 0.9 0.9', 0.5);
                }
                $pdf->text($r[0], 48, $y, 'F2', 8, '0.2 0.2 0.2');
                $pdf->text($r[1], 130, $y, 'F1', 8, '0.2 0.2 0.2');
                $pdf->text($r[2], 370, $y, 'F1', 8, '0.2 0.2 0.2');
                $pdf->text($r[3], 430, $y, 'F2', 8.5, '0.42 0.06 0.10');
                $pdf->text($r[4], 500, $y, 'F1', 8, '0.05 0.55 0.15');
                $y -= 22;
            }

            // Summary Card
            $pdf->rect(40, 360, 515, 45, '0.42 0.06 0.10', '0.98 0.96 0.96', 1);
            $pdf->text("TOTAL UNITS PASSED: 18.00", 50, 385, 'F2', 9, '0.42 0.06 0.10');
            $pdf->text("SEMESTRAL GENERAL WEIGHTED AVERAGE (GWA): {$gwa}", 50, 370, 'F2', 10, '0.42 0.06 0.10');
            $pdf->text("SCHOLARSHIP ELIGIBILITY STATUS: QUALIFIED", 300, 375, 'F2', 9, '0.05 0.55 0.15');

            // Certification Narrative
            $pdf->text("CERTIFICATION OF AUTHENTICITY", 50, 325, 'F2', 9, '0.2 0.2 0.2');
            $pdf->text("This certifies that the academic records and semestral grades indicated above are official, complete, and", 50, 310, 'F1', 8.5, '0.3 0.3 0.3');
            $pdf->text("accurately reflect the transcript file on permanent record at Cagayan State University - Lal-lo Campus.", 50, 298, 'F1', 8.5, '0.3 0.3 0.3');
            $pdf->text("This document is issued for scholarship screening and qualification verification purposes.", 50, 286, 'F1', 8.5, '0.3 0.3 0.3');

            // Signatures
            $pdf->line(50, 150, 230, 150, '0.3 0.3 0.3', 1);
            $pdf->text("MS. CLARA M. BAUTISTA", 75, 138, 'F2', 9, '0.2 0.2 0.2');
            $pdf->text("Records Officer & Evaluator", 85, 126, 'F1', 8, '0.4 0.4 0.4');

            $pdf->line(360, 150, 540, 150, '0.3 0.3 0.3', 1);
            $pdf->text("ENGR. ARISTOTLE P. GONZALES", 370, 138, 'F2', 9, '0.42 0.06 0.10');
            $pdf->text("Campus Registrar, CSU Lal-lo Campus", 375, 126, 'F1', 8, '0.4 0.4 0.4');

            // Official Seal Marker
            $pdf->rect(240, 95, 115, 65, '0.85 0.65 0.13', null, 1);
            $pdf->text("UNIVERSITY", 270, 142, 'F2', 7.5, '0.7 0.5 0.1');
            $pdf->text("OFFICIAL SEAL", 265, 130, 'F2', 7.5, '0.7 0.5 0.1');
            $pdf->text("CSU LAL-LO", 273, 118, 'F1', 7.5, '0.4 0.4 0.4');
            $pdf->text("2026-2027", 277, 106, 'F1', 7.5, '0.4 0.4 0.4');

            // Footer
            $pdf->text("VERIFICATION KEY: COG-" . strtoupper(substr(md5($studentId), 0, 10)) . " | Valid for AY 2026-2027", 140, 45, 'F3', 8, '0.4 0.4 0.4');
        });
    }

    /**
     * Generate Barangay Certificate of Indigency / Proof of Family Income.
     */
    public static function generateIndigency(Student $student): string
    {
        $fullName = strtoupper($student->user->full_name ?? 'STUDENT NAME');
        $muni = $student->municipality ?? 'Lal-lo';
        $brgy = $student->barangay ?? 'Centro';
        $income = number_format((float) ($student->monthly_household_income ?? 10000.00), 2);
        $is4ps = $student->is_4ps ? 'YES (Registered DSWD 4Ps Beneficiary Household)' : 'NO (Non-4Ps Resident)';

        return self::createPdf(function ($pdf) use ($fullName, $muni, $brgy, $income, $is4ps) {
            // Elegant borders
            $pdf->rect(25, 25, 545, 792, '0.1 0.3 0.15', null, 2);
            $pdf->rect(29, 29, 537, 784, '0.8 0.65 0.15', null, 0.75);

            // Barangay Header
            $pdf->text("REPUBLIC OF THE PHILIPPINES", 225, 795, 'F1', 8, '0.2 0.2 0.2');
            $pdf->text("PROVINCE OF CAGAYAN", 235, 782, 'F1', 8.5, '0.2 0.2 0.2');
            $pdf->text("MUNICIPALITY OF " . strtoupper($muni), 210, 768, 'F2', 10, '0.1 0.3 0.15');
            $pdf->text("BARANGAY " . strtoupper($brgy), 220, 754, 'F2', 12, '0.1 0.3 0.15');
            $pdf->text("OFFICE OF THE SANGGUNIANG BARANGAY", 185, 740, 'F1', 9, '0.3 0.3 0.3');

            $pdf->line(45, 730, 550, 730, '0.1 0.3 0.15', 1.5);
            $pdf->line(45, 726, 550, 726, '0.8 0.65 0.15', 0.5);

            // Title
            $pdf->text("BARANGAY CERTIFICATION OF INDIGENCY", 130, 685, 'F2', 14, '0.1 0.3 0.15');
            $pdf->text("& SOCIO-ECONOMIC FINANCIAL ELIGIBILITY", 160, 668, 'F2', 11, '0.8 0.55 0.1');

            // Salutation
            $pdf->text("TO WHOM IT MAY CONCERN:", 55, 625, 'F2', 10, '0.1 0.1 0.1');

            // Narrative Body
            $pdf->text("THIS IS TO CERTIFY that {$fullName}, of legal age, Filipino, single, and a bona fide resident", 65, 595, 'F1', 9.5, '0.15 0.15 0.15');
            $pdf->text("of Barangay {$brgy}, {$muni}, Cagayan, is personally known to this office to be a resident of good", 55, 580, 'F1', 9.5, '0.15 0.15 0.15');
            $pdf->text("moral character, a law-abiding citizen, and in peaceful standing within our community.", 55, 565, 'F1', 9.5, '0.15 0.15 0.15');

            $pdf->text("IT IS FURTHER CERTIFIED that according to the records, community census, and social assessment", 65, 535, 'F1', 9.5, '0.15 0.15 0.15');
            $pdf->text("conducted by the Barangay Council, the applicant's family belongs to the low-income / indigent", 55, 520, 'F1', 9.5, '0.15 0.15 0.15');
            $pdf->text("socio-economic bracket, having an estimated gross monthly household income of:", 55, 505, 'F1', 9.5, '0.15 0.15 0.15');

            // Income Highlight Box
            $pdf->rect(70, 440, 455, 45, '0.1 0.3 0.15', '0.95 0.98 0.95', 1);
            $pdf->text("ESTIMATED MONTHLY HOUSEHOLD INCOME: PHP {$income}", 115, 465, 'F2', 11, '0.1 0.3 0.15');
            $pdf->text("4Ps Beneficiary Status: {$is4ps}", 145, 450, 'F2', 8.5, '0.2 0.2 0.2');

            $pdf->text("The said family has no permanent source of regular income and is currently incapable of sustaining", 65, 410, 'F1', 9.5, '0.15 0.15 0.15');
            $pdf->text("tertiary educational expenditures without external financial assistance or government grants.", 55, 395, 'F1', 9.5, '0.15 0.15 0.15');

            $pdf->text("THIS CERTIFICATION is officially issued upon the request of the interested party as a supporting document", 65, 365, 'F1', 9.5, '0.15 0.15 0.15');
            $pdf->text("and requirement for their CHED / CSU-OSDW Scholarship & Financial Grant Application.", 55, 350, 'F1', 9.5, '0.15 0.15 0.15');

            $pdf->text("ISSUED AND SIGNED this 18th day of August, 2026 at the Barangay Hall of Barangay {$brgy}, {$muni}, Cagayan.", 55, 310, 'F4', 9, '0.2 0.2 0.2');

            // Signatures
            $pdf->line(60, 160, 240, 160, '0.3 0.3 0.3', 1);
            $pdf->text("MARIA S. TOLENTINO", 95, 148, 'F2', 9, '0.2 0.2 0.2');
            $pdf->text("Barangay Secretary", 110, 136, 'F1', 8, '0.4 0.4 0.4');

            $pdf->line(350, 160, 530, 160, '0.3 0.3 0.3', 1);
            $pdf->text("HON. EDUARDO M. PASCUAL", 370, 148, 'F2', 9, '0.1 0.3 0.15');
            $pdf->text("Punong Barangay (Barangay Captain)", 360, 136, 'F1', 8, '0.4 0.4 0.4');

            // Barangay Seal Stamp
            $pdf->rect(240, 85, 115, 60, '0.8 0.65 0.15', null, 1);
            $pdf->text("OFFICIAL SEAL", 265, 128, 'F2', 7.5, '0.7 0.5 0.1');
            $pdf->text("BRGY. " . strtoupper($brgy), 255, 116, 'F2', 7, '0.1 0.3 0.15');
            $pdf->text(strtoupper($muni) . ", CAGAYAN", 255, 104, 'F1', 6.5, '0.4 0.4 0.4');
            $pdf->text("SERIES OF 2026", 267, 92, 'F1', 6.5, '0.4 0.4 0.4');
        });
    }

    /**
     * Generate Valid Student Identification Card (ID).
     */
    public static function generateStudentID(Student $student): string
    {
        $fullName = strtoupper($student->user->full_name ?? 'STUDENT NAME');
        $studentId = $student->student_number ?? '26-00000';
        $course = $student->course ?? 'Bachelor of Science in Information Technology (BSIT)';
        $yearLevel = $student->year_level ?? '1st Year';
        $contact = $student->contact_number ?? '09171234567';

        return self::createPdf(function ($pdf) use ($fullName, $studentId, $course, $yearLevel, $contact) {
            // Document background
            $pdf->rect(25, 25, 545, 792, '0.42 0.06 0.10', null, 2);
            $pdf->rect(29, 29, 537, 784, '0.85 0.65 0.13', null, 0.75);

            // Header Banner
            $pdf->rect(30, 742, 535, 68, '0.42 0.06 0.10', '0.42 0.06 0.10', 0);
            $pdf->text("CAGAYAN STATE UNIVERSITY", 175, 780, 'F2', 15, '1 1 1');
            $pdf->text("LAL-LO CAMPUS - STA. MARIA, LAL-LO, CAGAYAN", 185, 765, 'F1', 9, '1 0.85 0.2');
            $pdf->text("OFFICIAL STUDENT IDENTIFICATION RECORD", 180, 750, 'F2', 9, '1 1 1');

            $pdf->text("DIGITAL STUDENT IDENTIFICATION CARD", 160, 715, 'F2', 13, '0.42 0.06 0.10');
            $pdf->text("Validated for Academic Year 2026-2027", 210, 700, 'F4', 10, '0.3 0.3 0.3');

            // ID Card Visual Graphic Box (Center of the page)
            $pdf->rect(70, 380, 455, 290, '0.42 0.06 0.10', '0.98 0.98 0.99', 2);

            // Card Header Banner
            $pdf->rect(72, 615, 451, 53, '0.42 0.06 0.10', '0.42 0.06 0.10', 0);
            $pdf->text("CAGAYAN STATE UNIVERSITY - LAL-LO CAMPUS", 135, 648, 'F2', 11, '1 1 1');
            $pdf->text("BONA FIDE UNDERGRADUATE STUDENT", 195, 630, 'F1', 8, '1 0.85 0.2');

            // Photo Placeholder
            $pdf->rect(90, 475, 110, 125, '0.42 0.06 0.10', '0.92 0.92 0.95', 1);
            $initials = strtoupper(substr($fullName, 0, 1) . substr(strrchr($fullName, ' ') ?: ' ', 1, 1));
            $pdf->text($initials, 125, 530, 'F2', 26, '0.42 0.06 0.10');
            $pdf->text("ID PHOTO", 125, 490, 'F1', 7.5, '0.5 0.5 0.5');

            // ID Card Fields
            $pdf->text("STUDENT ID NUMBER", 220, 585, 'F1', 7.5, '0.5 0.5 0.5');
            $pdf->text($studentId, 220, 570, 'F2', 15, '0.42 0.06 0.10');

            $pdf->text("NAME OF STUDENT", 220, 550, 'F1', 7.5, '0.5 0.5 0.5');
            $pdf->text($fullName, 220, 536, 'F2', 11, '0.1 0.1 0.1');

            $pdf->text("COURSE / DEGREE PROGRAM", 220, 518, 'F1', 7.5, '0.5 0.5 0.5');
            $pdf->text(substr($course, 0, 40), 220, 505, 'F1', 8.5, '0.2 0.2 0.2');

            $pdf->text("YEAR LEVEL: {$yearLevel}", 220, 488, 'F2', 8.5, '0.05 0.55 0.15');
            $pdf->text("CONTACT: {$contact}", 350, 488, 'F1', 8, '0.3 0.3 0.3');

            // Card Footer Barcode
            $pdf->line(90, 435, 505, 435, '0.8 0.8 0.8', 0.5);
            $pdf->text("||| | ||||| || |||||| | |||| |||||||| |||| |||||| |||||| | |||||||| ||", 150, 415, 'F3', 11, '0.1 0.1 0.1');
            $pdf->text("* {$studentId} * VALID UNTIL JULY 31, 2027 *", 200, 395, 'F1', 7.5, '0.4 0.4 0.4');

            // Back of the card details
            $pdf->rect(70, 180, 455, 170, '0.8 0.8 0.8', '0.96 0.96 0.97', 1);
            $pdf->text("TERMS AND CONDITIONS & EMERGENCY DETAILS", 85, 332, 'F2', 9, '0.42 0.06 0.10');
            $pdf->line(85, 326, 510, 326, '0.85 0.85 0.85', 0.5);

            $pdf->text("1. This identification card is non-transferable and remains property of Cagayan State University.", 85, 310, 'F1', 8, '0.3 0.3 0.3');
            $pdf->text("2. In case of emergency, please notify the Office of Student Development and Welfare (OSDW).", 85, 296, 'F1', 8, '0.3 0.3 0.3');
            $pdf->text("3. If found, please return to: Office of the Campus Registrar, CSU Lal-lo Campus, Cagayan.", 85, 282, 'F1', 8, '0.3 0.3 0.3');

            $pdf->line(280, 220, 480, 220, '0.4 0.4 0.4', 1);
            $pdf->text("DR. URDUJAH G. ALVARADO", 310, 208, 'F2', 8.5, '0.42 0.06 0.10');
            $pdf->text("University President & CEO", 335, 196, 'F1', 7.5, '0.4 0.4 0.4');

            // Footer
            $pdf->text("ELECTRONIC DOCUMENT VERIFICATION COPY FOR OSDW SCHOLARSHIP PORTAL", 110, 45, 'F4', 8, '0.5 0.5 0.5');
        });
    }

    /**
     * Generate Welfare Case Intake Assessment Form.
     */
    public static function generateWelfareIntake(WelfareCase $case, Student $student): string
    {
        $fullName = strtoupper($student->user->full_name ?? 'STUDENT NAME');
        $studentId = $student->student_number ?? '26-00000';
        $course = $student->course ?? 'Bachelor of Science in Information Technology (BSIT)';
        $yearLevel = $student->year_level ?? '1st Year';
        $contact = $student->contact_number ?? '09171234567';
        $caseId = $case->case_id ?? 'WC-2026-0001';
        $category = $case->category ?? 'Scholarship and Financial Assistance';
        $status = $case->status ?? 'Resolved';
        $desc = $case->description ?? 'Official student welfare consultation and assessment.';

        return self::createPdf(function ($pdf) use ($fullName, $studentId, $course, $yearLevel, $contact, $caseId, $category, $status, $desc) {
            // Double borders
            $pdf->rect(25, 25, 545, 792, '0.42 0.06 0.10', null, 2);
            $pdf->rect(29, 29, 537, 784, '0.85 0.65 0.13', null, 0.75);

            // Banner
            $pdf->rect(30, 742, 535, 68, '0.42 0.06 0.10', '0.42 0.06 0.10', 0);
            $pdf->text("CAGAYAN STATE UNIVERSITY - LAL-LO CAMPUS", 155, 780, 'F2', 13, '1 1 1');
            $pdf->text("OFFICE OF STUDENT DEVELOPMENT AND WELFARE (OSDW)", 140, 764, 'F1', 9.5, '1 0.85 0.2');
            $pdf->text("STUDENT WELFARE INTAKE ASSESSMENT & INTERVENTION FORM", 130, 750, 'F2', 9, '1 1 1');

            // Case Control Bar
            $pdf->rect(40, 685, 515, 38, '0.85 0.65 0.13', '0.98 0.96 0.90', 1);
            $pdf->text("CASE CONTROL ID: {$caseId}", 55, 705, 'F2', 11, '0.42 0.06 0.10');
            $pdf->text("MANDATE CATEGORY: " . strtoupper($category), 55, 692, 'F2', 8.5, '0.2 0.2 0.2');
            $pdf->text("STATUS: " . strtoupper($status), 420, 700, 'F2', 10, '0.05 0.55 0.15');

            // Student Information
            $pdf->rect(40, 580, 515, 90, '0.8 0.8 0.8', '0.96 0.96 0.97', 1);
            $pdf->text("CLIENT / STUDENT PERSONAL PROFILE", 50, 654, 'F2', 9, '0.42 0.06 0.10');
            $pdf->line(50, 648, 545, 648, '0.85 0.85 0.85', 0.5);

            $pdf->text("Student ID: {$studentId}", 50, 632, 'F2', 9, '0.1 0.1 0.1');
            $pdf->text("Name: {$fullName}", 50, 616, 'F2', 10, '0.1 0.1 0.1');
            $pdf->text("Degree Course: {$course}", 50, 600, 'F1', 8.5, '0.2 0.2 0.2');
            $pdf->text("Contact Number: {$contact}", 50, 586, 'F1', 8.5, '0.2 0.2 0.2');

            $pdf->text("Year Level: {$yearLevel}", 360, 632, 'F1', 9, '0.2 0.2 0.2');
            $pdf->text("Campus: CSU Lal-lo Campus", 360, 616, 'F1', 8.5, '0.2 0.2 0.2');
            $pdf->text("Intake Date: August 20, 2026", 360, 600, 'F1', 8.5, '0.3 0.3 0.3');

            // Narrative Intake Assessment
            $pdf->rect(40, 400, 515, 165, '0.8 0.8 0.8', '0.99 0.99 0.99', 1);
            $pdf->text("CASE INTAKE NARRATIVE & ASSESSED NEEDS", 50, 548, 'F2', 9, '0.42 0.06 0.10');
            $pdf->line(50, 542, 545, 542, '0.85 0.85 0.85', 0.5);

            $words = explode(' ', $desc);
            $line1 = implode(' ', array_slice($words, 0, 14));
            $line2 = implode(' ', array_slice($words, 14, 14));
            $line3 = implode(' ', array_slice($words, 28));

            $pdf->text("Statement of Concern / Request:", 50, 525, 'F2', 8.5, '0.2 0.2 0.2');
            $pdf->text($line1, 60, 508, 'F1', 8.5, '0.15 0.15 0.15');
            if ($line2) $pdf->text($line2, 60, 494, 'F1', 8.5, '0.15 0.15 0.15');
            if ($line3) $pdf->text($line3, 60, 480, 'F1', 8.5, '0.15 0.15 0.15');

            $pdf->text("Counselor Assessment & Preliminary Action:", 50, 455, 'F2', 8.5, '0.2 0.2 0.2');
            $pdf->text("Client has undergone formal initial intake interview. The presented documentation was evaluated", 60, 440, 'F1', 8, '0.3 0.3 0.3');
            $pdf->text("in compliance with the CSU OSDW standardized Student Welfare and Financial Assistance Framework.", 60, 428, 'F1', 8, '0.3 0.3 0.3');
            $pdf->text("Case endorsed to designated specialized unit for continuous monitoring and intervention.", 60, 416, 'F1', 8, '0.3 0.3 0.3');

            // Referral Action Section
            $pdf->rect(40, 260, 515, 125, '0.8 0.8 0.8', '0.97 0.98 0.97', 1);
            $pdf->text("OFFICIAL ENDORSEMENT & REFERRAL ACTION", 50, 370, 'F2', 9, '0.05 0.5 0.15');
            $pdf->line(50, 364, 545, 364, '0.85 0.85 0.85', 0.5);

            $pdf->text("Referral Type: Official Institutional Liaison & Service Endorsement", 50, 345, 'F1', 8.5, '0.2 0.2 0.2');
            $pdf->text("Recommendation: Endorsed for priority consideration under OSDW Welfare Mandates.", 50, 330, 'F1', 8.5, '0.2 0.2 0.2');
            $pdf->text("Follow-up Protocol: Monthly monitoring until resolution of academic/welfare concern.", 50, 315, 'F1', 8.5, '0.2 0.2 0.2');
            $pdf->text("Intervention Outcome: Case documented and filed in Student Information Repository.", 50, 300, 'F1', 8.5, '0.2 0.2 0.2');

            // Signatures
            $pdf->line(50, 130, 230, 130, '0.3 0.3 0.3', 1);
            $pdf->text("MS. ANGELICA V. RAMOS", 75, 118, 'F2', 9, '0.2 0.2 0.2');
            $pdf->text("OSDW Intake Guidance Counselor", 68, 106, 'F1', 8, '0.4 0.4 0.4');

            $pdf->line(360, 130, 540, 130, '0.3 0.3 0.3', 1);
            $pdf->text("DR. ROMMEL M. DELA CRUZ", 375, 118, 'F2', 9, '0.42 0.06 0.10');
            $pdf->text("Director, Student Development & Welfare", 365, 106, 'F1', 8, '0.4 0.4 0.4');

            // Footer
            $pdf->text("CONFIDENTIAL STUDENT RECORD - FOR OFFICIAL OSDW AUTHORIZED PERSONNEL ONLY", 85, 45, 'F4', 7.5, '0.5 0.5 0.5');
        });
    }
}
