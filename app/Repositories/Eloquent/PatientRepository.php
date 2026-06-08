<?php

namespace App\Repositories\Eloquent;

use App\Models\Patient;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * PatientRepository — Eloquent implementation of the Patient
 * data-access contract.
 *
 * Keeps all raw query logic in one place, away from controllers
 * and service classes.
 */
class PatientRepository implements PatientRepositoryInterface
{
    /**
     * @param Patient $model  Injected model instance for queries.
     */
    public function __construct(
        protected readonly Patient $model,
    ) {}

    /** {@inheritDoc} */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->latest()
            ->paginate($perPage);
    }

    /** {@inheritDoc} */
    public function findOrFail(int $id): Patient
    {
        /** @var Patient */
        return $this->model
            ->newQuery()
            ->findOrFail($id);
    }

    /** {@inheritDoc} */
    public function create(array $data): Patient
    {
        /** @var Patient */
        return $this->model
            ->newQuery()
            ->create($data);
    }

    /** {@inheritDoc} */
    public function update(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient->refresh();
    }

    /** {@inheritDoc} */
    public function delete(Patient $patient): bool
    {
        return (bool) $patient->delete();
    }
}
