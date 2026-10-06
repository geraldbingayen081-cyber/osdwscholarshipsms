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
        Schema::create('welfare_case_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('welfare_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('scholarship_id')->constrained()->cascadeOnDelete();
            $table->text('referral_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('welfare_case_referrals');
    }
};
