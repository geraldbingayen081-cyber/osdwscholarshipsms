<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            if (!Schema::hasColumn('scholarships', 'school_year')) {
                $table->string('school_year')->nullable()->after('provider')->comment('Formatted YYYY-YYYY e.g. 2026-2027');
            }
            $table->unsignedBigInteger('academic_year_id')->nullable()->change();
            $table->unsignedBigInteger('semester_id')->nullable()->change();
            $table->string('coverage_type')->default('semester')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarships', function (Blueprint $table) {
            if (Schema::hasColumn('scholarships', 'school_year')) {
                $table->dropColumn('school_year');
            }
        });
    }
};
