<?php

use App\Http\Controllers\AI\MedicalImagingController;
use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Stateless RESTful API endpoints for the Healthcare application.
| All routes are prefixed with `/api` automatically by Laravel.
|
| Sanctum middleware can be applied per-group when authentication
| is enforced (see commented example below).
|
*/

// ── Public / Unprotected Routes ──────────────────────────────────────

Route::prefix('v1')->group(function (): void {

    // ── Patient CRUD ─────────────────────────────────────────────
    Route::apiResource('patients', PatientController::class);

    // ── AI Diagnostic (patient-scoped) ───────────────────────────
    Route::post('patients/{patient}/diagnose', [PatientController::class, 'diagnose'])
         ->name('patients.diagnose');

    // ── Medical Imaging — Diabetic Retinopathy Detection ─────────
    // POST /api/v1/medical-imaging/analyze
    //
    // Accepts : multipart/form-data → field `eye_image` (jpeg|png|jpg, max 10 MB)
    // Returns : { success, message, data: { prediction, confidence } }
    // Errors  : 422 on validation failure | 503 when FastAPI is unreachable
    Route::post('medical-imaging/analyze', [MedicalImagingController::class, 'analyzeEyeImage'])
         ->name('medical-imaging.analyze');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (uncomment when Sanctum is installed)
|--------------------------------------------------------------------------
|
| Route::middleware('auth:sanctum')->prefix('v1')->group(function (): void {
|     Route::apiResource('patients', PatientController::class);
|     Route::post('patients/{patient}/diagnose', [PatientController::class, 'diagnose'])
|          ->name('patients.diagnose');
| });
|
*/
