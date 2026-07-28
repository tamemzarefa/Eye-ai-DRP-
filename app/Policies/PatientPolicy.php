<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isDoctor();
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->isDoctor();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isDoctor();
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->isDoctor();
    }

    public function diagnose(User $user, Patient $patient): bool
    {
        return $user->isDoctor();
    }
}
