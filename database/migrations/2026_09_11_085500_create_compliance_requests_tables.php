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
        // 1. Compliance Requests (Periodic Compliance definition by Admin)
        Schema::create('compliance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained('scholarships')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->string('school_year');
            $table->string('semester');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->date('deadline');
            $table->enum('status', ['active', 'closed', 'extended', 'completed'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. Compliance Requirements (Items to submit: COE, School ID, etc.)
        Schema::create('compliance_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compliance_request_id')->constrained('compliance_requests')->onDelete('cascade');
            $table->string('name');
            $table->text('instruction')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        // 3. Scholar Compliances (Assigned compliance record per active scholar)
        Schema::create('scholar_compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compliance_request_id')->constrained('compliance_requests')->onDelete('cascade');
            $table->foreignId('scholar_id')->constrained('scholars')->onDelete('cascade');
            $table->enum('status', [
                'not_submitted',
                'partially_submitted',
                'submitted',
                'under_review',
                'completed',
                'overdue',
                'needs_correction'
            ])->default('not_submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->unique(['compliance_request_id', 'scholar_id']);
        });

        // 4. Scholar Compliance Documents (Uploaded files per requirement item)
        Schema::create('scholar_compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholar_compliance_id')->constrained('scholar_compliances')->onDelete('cascade');
            $table->foreignId('compliance_requirement_id')->constrained('compliance_requirements')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_filename');
            $table->text('student_remarks')->nullable();
            $table->enum('verification_status', [
                'pending',
                'verified',
                'needs_correction',
                'rejected'
            ])->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholar_compliance_documents');
        Schema::dropIfExists('scholar_compliances');
        Schema::dropIfExists('compliance_requirements');
        Schema::dropIfExists('compliance_requests');
    }
};
