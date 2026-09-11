<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default institutional settings
        $defaults = [
            'system_name' => 'OSDW Scholarship Management System',
            'system_acronym' => 'OSDW-SMS',
            'institution_name' => 'Cagayan State University',
            'campus_name' => 'Lal-lo Campus',
            'office_name' => 'Office of Student Development & Welfare',
            'contact_email' => 'osdw.lallo@csu.edu.ph',
            'contact_phone' => '(078) 123-4567',
            'address' => 'Sta. Maria, Lal-lo, Cagayan 3509',
            'system_logo_path' => null,
        ];

        foreach ($defaults as $key => $value) {
            DB::table('system_settings')->insertOrIgnore([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
