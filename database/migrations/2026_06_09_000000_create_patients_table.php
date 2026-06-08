<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the `patients` table for the Healthcare module.
     * Stores core patient demographic and status information.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table): void {
            $table->id();

            // ── Core Demographics ──────────────────────────────────
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other'])->default('other');

            // ── Clinical Status ────────────────────────────────────
            $table->enum('status', ['active', 'inactive', 'archived'])
                  ->default('active')
                  ->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
