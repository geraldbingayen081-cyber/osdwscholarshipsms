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
        Schema::create('welfare_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_id')->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('category')->default('Financial Assistance'); // 'Financial Assistance', 'Other', etc.
            $table->text('description');
            $table->text('requested_information')->nullable();
            $table->string('status')->default('Open'); // 'Open', 'Under Assessment', 'Referred', 'For Follow-up', 'Resolved', 'Closed'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welfare_cases');
    }
};
