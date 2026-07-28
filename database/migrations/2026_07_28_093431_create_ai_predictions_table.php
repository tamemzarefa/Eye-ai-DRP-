<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create the `ai_predictions` table.
 *
 * Stores the complete output from the FastAPI AI server for a single
 * retinal scan. Captures all three model outputs:
 *   1. Classification  → predicted_class + confidence_score
 *   2. Segmentation    → segmentation_mask_path (private disk)
 *   3. Heatmap         → heatmap_path (private disk)
 *
 * The raw JSON response from FastAPI is also preserved for auditing.
 * This table is the single source of truth for AI history.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_predictions', function (Blueprint $table): void {
            $table->id();

            // One-to-one — each scan produces at most one prediction record
            $table->foreignId('retinal_scan_id')
                  ->unique()
                  ->constrained('retinal_scans')
                  ->cascadeOnDelete();

            // ── Classification Output ─────────────────────────────────
            $table->enum('predicted_class', [
                'Negative',
                'Mild',
                'Moderate',
                'Severe',
                'Proliferative',
            ])->index();

            // Confidence score as percentage (e.g. 97.24)
            $table->decimal('confidence_score', 5, 2);

            // ── Segmentation Output ───────────────────────────────────
            // Relative path on private disk; null if not requested/failed
            $table->string('segmentation_mask_path')->nullable();

            // ── Heatmap Output ────────────────────────────────────────
            // Relative path on private disk; null if not requested/failed
            $table->string('heatmap_path')->nullable();

            // ── Audit & Debugging ─────────────────────────────────────
            // Full JSON payload from FastAPI (preserved for auditing)
            $table->json('raw_response_log')->nullable();

            // When FastAPI completed processing
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_predictions');
    }
};
