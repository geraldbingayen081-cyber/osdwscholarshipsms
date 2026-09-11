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
            $table->date('renewal_deadline')->nullable()->after('application_deadline');
        });

        Schema::create('scholar_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholar_id')->constrained('scholars')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->string('file_path');
            $table->string('original_filename');
            $table->enum('status', ['pending', 'verified', 'needs_resubmission'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholar_renewals');

        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn('renewal_deadline');
        });
    }
};
