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
        Schema::table('scholar_renewals', function (Blueprint $table) {
            if (!Schema::hasColumn('scholar_renewals', 'requirement_type')) {
                $table->string('requirement_type')->nullable()->after('semester_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholar_renewals', function (Blueprint $table) {
            if (Schema::hasColumn('scholar_renewals', 'requirement_type')) {
                $table->dropColumn('requirement_type');
            }
        });
    }
};
