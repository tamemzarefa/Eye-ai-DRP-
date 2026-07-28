<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `doctor_diagnoses` table.
 *
 * Records the doctor's final clinical decision for an entire examination.
 * The doctor reviews the AI prediction(s), may agree or override, and
 * writes clinical notes. This is the authoritative human record.
 *
 * One examination → one doctor diagnosis record.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_diagnoses', function (Blueprint $table): void {
            $table->id();

            // One-to-one — one diagnosis per examination
            $table->foreignId('examination_id')
                  ->unique()
                  ->constrained('eye_examinations')
                  ->cascadeOnDelete();

            // The doctor who issued the final diagnosis
            $table->foreignId('doctor_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // The doctor's authoritative diagnosis (may differ from AI)
            $table->enum('final_diagnosis', [
                'Negative',
                'Mild',
                'Moderate',
                'Severe',
                'Proliferative',
            ])->index();

            // The doctor's clinical notes and recommendations
            $table->text('doctor_notes')->nullable();

            // Whether the doctor agreed with, disagreed with, or was unsure about the AI
            $table->enum('ai_feedback', ['agree', 'disagree', 'unsure'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_diagnoses');
    }
};
