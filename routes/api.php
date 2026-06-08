<?php

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

    // ── AI Diagnostic (standalone endpoint) ──────────────────────
    Route::post('patients/{patient}/diagnose', [PatientController::class, 'diagnose'])
         ->name('patients.diagnose');
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
