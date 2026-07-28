<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extend the `patients` table.
 *
 * Changes:
 *  - `deleted_at` : soft deletes — never permanently destroy patient records.
 *  - Index on `name` for faster search queries.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            // Soft deletes — mandatory for medical data protection
            $table->softDeletes()->after('status');

            // Index for faster name-based patient search
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table): void {
            $table->dropSoftDeletes();
            $table->dropIndex(['name']);
        });
    }
};
