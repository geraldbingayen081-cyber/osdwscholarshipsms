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
        Schema::create('scholars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('scholarship_id')->constrained('scholarships')->onDelete('cascade');
            $table->foreignId('application_id')->unique()->constrained('applications')->onDelete('cascade');
            $table->enum('status', ['active', 'for_renewal', 'completed', 'terminated'])->default('active');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholars');
    }
};
