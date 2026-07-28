<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `eye_examinations` table.
 *
 * Represents a single clinical visit performed by a doctor for a patient.
 * One patient may have many examinations over time.
 * Each examination contains one or more retinal scans (left eye, right eye).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eye_examinations', function (Blueprint $table): void {
            $table->id();

            // The patient being examined
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->cascadeOnDelete();

            // The authenticated doctor who performed the examination
            $table->foreignId('doctor_id')
                  ->constrained('users')
                  ->restrictOnDelete(); // Prevent deleting a doctor with examination history

            // When the examination was conducted
            $table->date('examination_date');

            // General clinical notes for the entire visit (optional)
            $table->text('clinical_notes')->nullable();

            $table->timestamps();

            // Index for querying a patient's examination history
            $table->index(['patient_id', 'examination_date']);
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eye_examinations');
    }
};
