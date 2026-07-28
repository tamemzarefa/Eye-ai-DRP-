<?php

namespace App\Policies;

use App\Models\EyeExamination;
use App\Models\User;

class EyeExaminationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, EyeExamination $examination): bool
    {
        return $user->isDoctor();
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    public function update(User $user, EyeExamination $examination): bool
    {
        return $user->isDoctor();
    }

    public function delete(User $user, EyeExamination $examination): bool
    {
        return $user->isDoctor();
    }
}
