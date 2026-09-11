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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('scholarship_id')->constrained('scholarships')->onDelete('cascade');
            $table->string('status')->default('submitted');
            $table->text('remarks')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Duplicate application prevention at DB level
            $table->unique(['student_id', 'scholarship_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
