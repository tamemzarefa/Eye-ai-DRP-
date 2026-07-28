<?php

namespace App\Services;

use App\Models\Patient;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\AiInferenceOrchestrator;

/**
 * PatientService — Business logic orchestrator for the Patient module.
 *
 * Coordinates between the repository (persistence), Spatie Media
 * Library (file management), and the FastApiService (AI inference).
 * Controllers delegate all logic here to stay slim.
 */
class PatientService
{
    public function __construct(
        protected readonly PatientRepositoryInterface $patientRepository,
        protected readonly FastApiService $fastApiService,
        protected readonly AiInferenceOrchestrator $aiInferenceOrchestrator,
    ) {}

    // ─── Query Methods ────────────────────────────────────────────

    /**
     * Get a paginated list of patients.
     *
     * @param  int $perPage  Records per page.
     * @return LengthAwarePaginator<Patient>
     */
    public function listPatients(int $perPage = 15): LengthAwarePaginator
    {
        return $this->patientRepository->paginate($perPage);
    }

    /**
     * Retrieve a single patient by ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getPatient(int $id): Patient
    {
        return $this->patientRepository->findOrFail($id);
    }

    // ─── Command Methods ──────────────────────────────────────────

    /**
     * Create a new patient, optionally attach a profile image,
     * and optionally run AI diagnostics on the uploaded image.
     *
     * Wraps everything in a DB transaction for atomicity.
     *
     * @param  array<string, mixed>  $data         Validated patient attributes.
     * @param  UploadedFile|null     $profileImage  Optional profile image upload.
     * @param  bool                  $runDiagnostic Whether to send the image to FastAPI.
     * @return array{patient: Patient, diagnosis: array<string, mixed>|null}
     */
    public function createPatient(
        array $data,
        ?UploadedFile $profileImage = null,
        bool $runDiagnostic = false,
    ): array {
        $diagnosis = null;

        /** @var Patient $patient */
        $patient = DB::transaction(function () use ($data, $profileImage): Patient {
            // 1. Persist the patient record
            $patient = $this->patientRepository->create($data);

            // 2. Attach profile image via Spatie Media Library
            if ($profileImage !== null) {
                $patient->addMedia($profileImage)
                    ->usingName($patient->name . ' — Profile')
                    ->toMediaCollection('profile_image');
            }

            return $patient;
        });

        // 3. (Optional) Send image to FastAPI for AI analysis
        if ($runDiagnostic && $profileImage !== null) {
            $diagnosis = $this->createDiagnosticExamination($patient, $profileImage);
        }

        return [
            'patient'   => $patient->load('media'),
            'diagnosis' => $diagnosis,
        ];
    }

    /**
     * Create an initial eye examination for the patient and run AI inference.
     */
    protected function createDiagnosticExamination(Patient $patient, UploadedFile $profileImage): ?array
    {
        $doctorId = Auth::id();

        if ($doctorId === null) {
            Log::warning('PatientService: Cannot create examination without authenticated doctor.');

            return null;
        }

        $examination = $patient->examinations()->create([
            'doctor_id'        => $doctorId,
            'examination_date' => now()->toDateString(),
            'clinical_notes'   => 'Automated diagnostic scan created during patient registration.',
        ]);

        $filename = Str::uuid()->toString() . '.' . $profileImage->getClientOriginalExtension();
        $storagePath = $profileImage->storeAs(
            'retinal-scans/' . now()->format('Y/m/d'),
            $filename,
            'private'
        );

        $retinalScan = $examination->retinalScans()->create([
            'eye'                => 'left',
            'original_scan_path' => $storagePath,
            'status'             => 'pending',
        ]);

        $prediction = $this->aiInferenceOrchestrator->processScan($retinalScan);

        if ($prediction === null) {
            return null;
        }

        return [
            'predicted_class' => $prediction->predicted_class,
            'confidence_score'=> $prediction->confidence_score,
            'segmentation_url'=> $prediction->segmentationUrl(),
            'heatmap_url'     => $prediction->heatmapUrl(),
        ];
    }

    /**
     * Update an existing patient and optionally replace the profile image.
     *
     * @param  int                   $id            Patient primary key.
     * @param  array<string, mixed>  $data          Validated patient attributes.
     * @param  UploadedFile|null     $profileImage  Optional new profile image.
     * @return Patient
     */
    public function updatePatient(
        int $id,
        array $data,
        ?UploadedFile $profileImage = null,
    ): Patient {
        $patient = $this->patientRepository->findOrFail($id);

        return DB::transaction(function () use ($patient, $data, $profileImage): Patient {
            $patient = $this->patientRepository->update($patient, $data);

            if ($profileImage !== null) {
                // singleFile() in the collection auto-removes the old one
                $patient->addMedia($profileImage)
                    ->usingName($patient->name . ' — Profile')
                    ->toMediaCollection('profile_image');
            }

            return $patient->load('media');
        });
    }

    /**
     * Delete a patient and all associated media.
     */
    public function deletePatient(int $id): bool
    {
        $patient = $this->patientRepository->findOrFail($id);

        return $this->patientRepository->delete($patient);
    }

    // ─── AI Diagnostics ───────────────────────────────────────────

    /**
     * Send the patient's profile image to the FastAPI model and
     * return the raw prediction payload.
     *
     * @return array<string, mixed>|null  Null if no media is attached.
     */
    public function runAiDiagnostic(Patient $patient): ?array
    {
        $media = $patient->getFirstMedia('profile_image');

        if ($media === null) {
            Log::warning('PatientService: No profile image to diagnose.', [
                'patient_id' => $patient->id,
            ]);

            return null;
        }

        try {
            return $this->fastApiService->analyzeImage(
                imagePath: $media->getPath(),
                metadata: [
                    'patient_id'    => $patient->id,
                    'patient_name'  => $patient->name,
                    'date_of_birth' => $patient->date_of_birth->toDateString(),
                ],
            );
        } catch (\Throwable $e) {
            Log::error('PatientService: AI diagnostic failed.', [
                'patient_id' => $patient->id,
                'error'      => $e->getMessage(),
            ]);

            return null;
        }
    }
}
