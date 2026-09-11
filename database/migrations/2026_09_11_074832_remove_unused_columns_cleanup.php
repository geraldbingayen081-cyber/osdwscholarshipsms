<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove confirmed-unused columns that are redundant or were never referenced
     * in any view, controller, or business logic.
     *
     * Removed:
     *   - users.student_id                           – never queried or displayed
     *   - applications.rejection_reason              – duplicate of `remarks`; nothing reads it
     *   - application_documents.doc_type             – never used in any view or controller
     *   - application_documents.verification_status – redundant with existing `status` column
     */
    public function up(): void
    {
        // 1. Drop users.student_id
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_id')) {
                $table->dropColumn('student_id');
            }
        });

        // 2. Drop applications.rejection_reason
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });

        // 3. Drop application_documents.doc_type and application_documents.verification_status
        Schema::table('application_documents', function (Blueprint $table) {
            $toDrop = [];
            if (Schema::hasColumn('application_documents', 'doc_type')) {
                $toDrop[] = 'doc_type';
            }
            if (Schema::hasColumn('application_documents', 'verification_status')) {
                $toDrop[] = 'verification_status';
            }
            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }

    /**
     * Reverse the migrations (restore the removed columns).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_id')) {
                $table->string('student_id')->nullable()->after('id')
                      ->comment('Nullable for admin/staff, set for students');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
        });

        Schema::table('application_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('application_documents', 'doc_type')) {
                $table->string('doc_type')->nullable()->after('scholarship_requirement_id')
                      ->comment('COR, COG, Indigency, ID');
            }
            if (!Schema::hasColumn('application_documents', 'verification_status')) {
                $table->string('verification_status')->default('pending')->after('file_path')
                      ->comment('pending, verified, rejected');
            }
        });
    }
};
