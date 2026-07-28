<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extend the `users` table for the doctor-facing EyeAI platform.
 *
 * Changes:
 *  - `role`       : distinguishes between 'admin' and 'doctor' users.
 *                   Patients do NOT have user accounts in this system.
 *  - `deleted_at` : soft deletes so doctor accounts can be deactivated
 *                   without destroying audit history.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Role — only admin and doctor exist in this system
            $table->enum('role', ['admin', 'doctor'])
                  ->default('doctor')
                  ->after('email_verified_at')
                  ->index();

            // Soft deletes — preserve history when a doctor is removed
            $table->softDeletes()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
            $table->dropSoftDeletes();
        });
    }
};
