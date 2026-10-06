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
        Schema::table('welfare_case_referrals', function (Blueprint $table) {
            $table->foreignId('scholarship_id')->nullable()->change();
            $table->string('referral_type')->default('scholarship')->after('scholarship_id');
            $table->string('referred_to_office')->nullable()->after('referral_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('welfare_case_referrals', function (Blueprint $table) {
            $table->dropColumn(['referral_type', 'referred_to_office']);
        });
    }
};
