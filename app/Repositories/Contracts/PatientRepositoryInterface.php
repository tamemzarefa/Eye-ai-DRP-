<?php

namespace App\Repositories\Contracts;

use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * PatientRepositoryInterface — Data-access contract for Patients.
 *
 * All database operations on patients flow through this interface,
 * enabling easy swapping of the concrete implementation (Eloquent,
 * API-backed, or an in-memory fake for tests).
 */
interface PatientRepositoryInterface
{
    /**
     * Retrieve a paginated listing of patients.
     *
     * @param  int $perPage  Number of records per page.
     * @return LengthAwarePaginator<Patient>
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a single patient by primary key or fail.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Patient;

    /**
     * Persist a new patient record.
     *
     * @param  array<string, mixed> $data  Validated patient attributes.
     */
    public function create(array $data): Patient;

    /**
     * Update an existing patient record.
     *
     * @param  Patient              $patient  The patient instance.
     * @param  array<string, mixed> $data     Validated patient attributes.
     */
    public function update(Patient $patient, array $data): Patient;

    /**
     * Delete a patient record.
     */
    public function delete(Patient $patient): bool;
}
