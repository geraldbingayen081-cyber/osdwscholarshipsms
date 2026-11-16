<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\WelfareCase;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mappings = [
            'Financial Assistance' => WelfareCase::CATEGORY_SCHOLARSHIP,
            'Guidance and Counseling' => WelfareCase::CATEGORY_ASSISTANTSHIP,
            'Medical / Health Concern' => WelfareCase::CATEGORY_SMAP,
            'Health / Medical Concern' => WelfareCase::CATEGORY_SMAP,
            'Academic Grievance' => WelfareCase::CATEGORY_CO_CURRICULAR,
            'Academic Hardship' => WelfareCase::CATEGORY_CO_CURRICULAR,
            'Student Conduct / Discipline' => WelfareCase::CATEGORY_ORG_ADVISING,
            'Housing / Dormitory' => WelfareCase::CATEGORY_SMAP,
            'Family / Personal Crisis' => WelfareCase::CATEGORY_SMAP,
            'Emergency Relief' => WelfareCase::CATEGORY_SMAP,
            'Other' => WelfareCase::CATEGORY_SCHOLARSHIP,
            'Other Welfare Concern' => WelfareCase::CATEGORY_SCHOLARSHIP,
        ];

        foreach ($mappings as $oldCategory => $newCategory) {
            DB::table('welfare_cases')
                ->where('category', $oldCategory)
                ->update(['category' => $newCategory]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('welfare_cases')
            ->where('category', WelfareCase::CATEGORY_SCHOLARSHIP)
            ->update(['category' => 'Financial Assistance']);
    }
};
