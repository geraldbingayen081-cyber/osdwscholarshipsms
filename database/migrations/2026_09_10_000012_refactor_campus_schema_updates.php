<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the non-destructive refactoring schema updates for CSU Lal-lo OSDW System.
     */
    public function up(): void
    {
        // 1. Refactor Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('id')->comment('Nullable for admin/staff, set for students');
            }
        });

        // 2. Refactor Students / Student Profiles Table
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'college')) {
                $table->string('college')->nullable()->after('user_id')->comment('CAg, CHM, CICS, CTE');
            }
            if (!Schema::hasColumn('students', 'program')) {
                $table->string('program')->nullable()->after('college')->comment('Academic degree program');
            }
            if (!Schema::hasColumn('students', 'current_gwa')) {
                $table->decimal('current_gwa', 3, 2)->nullable()->after('year_level');
            }
            if (!Schema::hasColumn('students', 'monthly_household_income')) {
                $table->decimal('monthly_household_income', 10, 2)->nullable()->after('current_gwa');
            }
            if (!Schema::hasColumn('students', 'municipality')) {
                $table->string('municipality')->nullable()->after('monthly_household_income');
            }
            if (!Schema::hasColumn('students', 'barangay')) {
                $table->string('barangay')->nullable()->after('municipality');
            }
            if (!Schema::hasColumn('students', 'is_4ps')) {
                $table->boolean('is_4ps')->default(false)->after('barangay');
            }
        });

        // 3. Refactor Scholarships Table
        Schema::table('scholarships', function (Blueprint $table) {
            if (!Schema::hasColumn('scholarships', 'min_gwa')) {
                $table->decimal('min_gwa', 3, 2)->nullable()->after('benefits');
            }
            if (!Schema::hasColumn('scholarships', 'max_household_income')) {
                $table->decimal('max_household_income', 10, 2)->nullable()->after('min_gwa');
            }
            if (!Schema::hasColumn('scholarships', 'is_mutually_exclusive')) {
                $table->boolean('is_mutually_exclusive')->default(false)->after('max_household_income');
            }
        });

        // 4. Refactor Applications Table
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'student_gwa')) {
                $table->decimal('student_gwa', 3, 2)->nullable()->after('scholarship_id');
            }
            if (!Schema::hasColumn('applications', 'monthly_income')) {
                $table->decimal('monthly_income', 10, 2)->nullable()->after('student_gwa');
            }
            if (!Schema::hasColumn('applications', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });

        // 5. Refactor Application Documents Table
        Schema::table('application_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('application_documents', 'doc_type')) {
                $table->string('doc_type')->nullable()->after('scholarship_requirement_id')->comment('COR, COG, Indigency, ID');
            }
            if (!Schema::hasColumn('application_documents', 'verification_status')) {
                $table->string('verification_status')->default('pending')->after('file_path')->comment('pending, verified, rejected');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'college',
                'program',
                'current_gwa',
                'monthly_household_income',
                'municipality',
                'barangay',
                'is_4ps',
            ]);
        });

        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn([
                'min_gwa',
                'max_household_income',
                'is_mutually_exclusive',
            ]);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'student_gwa',
                'monthly_income',
                'rejection_reason',
            ]);
        });

        Schema::table('application_documents', function (Blueprint $table) {
            $table->dropColumn([
                'doc_type',
                'verification_status',
            ]);
        });
    }
};
