<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * PatientController — Slim RESTful API controller for the
 * Patient module.
 *
 * All business logic is delegated to PatientService.
 * All validation is handled by dedicated FormRequest classes.
 * All responses are wrapped in PatientResource for consistency.
 */
class PatientController extends Controller
{
    public function __construct(
        protected readonly PatientService $patientService,
    ) {
        $this->authorizeResource(Patient::class, 'patient');
    }

    // ─── GET /api/patients ────────────────────────────────────────

    /**
     * Display a paginated listing of patients.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        $patients = $this->patientService->listPatients($perPage);

        return PatientResource::collection($patients);
    }

    // ─── POST /api/patients ───────────────────────────────────────

    /**
     * Store a newly created patient.
     *
     * Optionally accepts a `profile_image` file and a
     * `run_diagnostic` boolean flag to trigger AI analysis.
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->patientService->createPatient(
            data: collect($validated)->except(['profile_image', 'run_diagnostic'])->toArray(),
            profileImage: $request->file('profile_image'),
            runDiagnostic: (bool) ($validated['run_diagnostic'] ?? false),
        );

        return response()->json([
            'message'   => 'Patient created successfully.',
            'data'      => new PatientResource($result['patient']),
            'diagnosis' => $result['diagnosis'],
        ], JsonResponse::HTTP_CREATED);  // 201
    }

    // ─── GET /api/patients/{patient} ──────────────────────────────

    /**
     * Display the specified patient.
     */
    public function show(int $patient): PatientResource
    {
        $model = $this->patientService->getPatient($patient);

        return new PatientResource($model);
    }

    // ─── PUT|PATCH /api/patients/{patient} ────────────────────────

    /**
     * Update the specified patient.
     */
    public function update(UpdatePatientRequest $request, int $patient): JsonResponse
    {
        $validated = $request->validated();

        $updated = $this->patientService->updatePatient(
            id: $patient,
            data: collect($validated)->except('profile_image')->toArray(),
            profileImage: $request->file('profile_image'),
        );

        return response()->json([
            'message' => 'Patient updated successfully.',
            'data'    => new PatientResource($updated),
        ]);
    }

    // ─── DELETE /api/patients/{patient} ───────────────────────────

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(int $patient): JsonResponse
    {
        $this->patientService->deletePatient($patient);

        return response()->json([
            'message' => 'Patient deleted successfully.',
        ]);
    }

    // ─── POST /api/patients/{patient}/diagnose ────────────────────

    /**
     * Trigger AI diagnostic analysis on an existing patient's
     * profile image via the FastAPI service.
     */
    public function diagnose(int $patient): JsonResponse
    {
        $model  = $this->patientService->getPatient($patient);
        $this->authorize('diagnose', $model);

        $result = $this->patientService->runAiDiagnostic($model);

        if ($result === null) {
            return response()->json([
                'message' => 'No profile image available for diagnosis.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        return response()->json([
            'message'   => 'Diagnosis completed successfully.',
            'data'      => new PatientResource($model),
            'diagnosis' => $result,
        ]);
    }
}
