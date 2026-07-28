<?php

use App\Http\Controllers\AI\MedicalImagingController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SecureFileController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API endpoints for the Healthcare application.
| All routes are prefixed with `/api` automatically by Laravel.
|
*/

Route::prefix('v1')->group(function (): void {
    // ── Authentication ────────────────────────────────────────────
    Route::post('auth/login', [AuthController::class, 'login'])
         ->name('auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout'])
             ->name('auth.logout');

        Route::get('auth/me', [AuthController::class, 'me'])
             ->name('auth.me');

        // ── Patient CRUD ─────────────────────────────────────────
        Route::apiResource('patients', PatientController::class);

        // ── AI Diagnostic (patient-scoped) ───────────────────────
        Route::post('patients/{patient}/diagnose', [PatientController::class, 'diagnose'])
             ->name('patients.diagnose');

        // ── Report persistence ───────────────────────────────────
        Route::post('reports', [ReportController::class, 'store'])
             ->name('reports.store');
        Route::get('reports/{report}', [ReportController::class, 'show'])
             ->name('reports.show');

        // ── Medical Imaging — Diabetic Retinopathy Detection ────
        // POST /api/v1/medical-imaging/analyze
        //
        // Accepts : multipart/form-data → field `eye_image` (jpeg|png|jpg, max 10 MB)
        // Returns : { success, message, data: { prediction, confidence } }
        // Errors  : 422 on validation failure | 503 when FastAPI is unreachable
        Route::post('medical-imaging/analyze', [MedicalImagingController::class, 'analyzeEyeImage'])
             ->name('medical-imaging.analyze');

        Route::get('scans/{scan}/preview', [SecureFileController::class, 'previewScan'])
             ->name('scans.preview');

        Route::get('predictions/{prediction}/asset/{asset}', [SecureFileController::class, 'asset'])
             ->where('asset', 'segmentation|heatmap')
             ->name('predictions.asset');
    });
});
