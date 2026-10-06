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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('log_type', 50)->index(); // Authentication, Scholarship, Application, Welfare Case, Compliance, Scholar, Student, Settings, Export, Security
            $table->string('action', 50)->index();   // login, logout, failed_login, create, update, delete, status_change, referral, verify_document, export_csv, etc.
            $table->text('description');
            $table->nullableMorphs('subject');       // subject_type, subject_id
            $table->json('properties')->nullable();  // metadata, old/new changes, request context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
