<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `retinal_scans` table.
 *
 * Stores the original uploaded retinal scan images for each eye
 * within an examination. One examination can have up to two scans
 * (left eye and right eye).
 *
 * Images are stored on a private disk — never accessible directly
 * via a public URL. Delivery goes through SecureFileService.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retinal_scans', function (Blueprint $table): void {
            $table->id();

            // Links to the clinical visit
            $table->foreignId('examination_id')
                  ->constrained('eye_examinations')
                  ->cascadeOnDelete();

            // Which eye the scan belongs to
            $table->enum('eye', ['left', 'right']);

            // Relative path to the private disk (e.g. "retinal-scans/2026/07/scan.jpg")
            $table->string('original_scan_path');

            // Processing status — updated when AI inference completes or fails
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])
                  ->default('pending')
                  ->index();

            $table->timestamps();

            // A unique constraint — only one scan per eye per examination
            $table->unique(['examination_id', 'eye']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retinal_scans');
    }
};
