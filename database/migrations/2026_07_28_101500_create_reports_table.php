<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->string('patient_name');
            $table->string('patient_age')->nullable();
            $table->string('patient_id');
            $table->string('classification');
            $table->decimal('confidence', 5, 2);
            $table->text('doctor_notes')->nullable();
            $table->string('feedback')->nullable();
            $table->string('image_preview')->nullable();
            $table->string('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
