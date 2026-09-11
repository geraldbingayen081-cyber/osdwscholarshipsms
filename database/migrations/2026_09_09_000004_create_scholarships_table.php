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
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->string('name');
            $table->string('provider');
            $table->text('description');
            $table->text('benefits');
            $table->unsignedInteger('available_slots');
            $table->date('application_start_date');
            $table->date('application_deadline');
            $table->enum('coverage_type', ['semester', 'academic_year'])->default('semester');
            $table->enum('status', ['draft', 'open', 'closed', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
