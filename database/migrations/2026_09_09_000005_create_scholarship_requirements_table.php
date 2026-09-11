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
        Schema::create('scholarship_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_id')->constrained('scholarships')->onDelete('cascade');
            $table->string('requirement_name');
            $table->enum('requirement_type', ['eligibility', 'document']);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_requirements');
    }
};
