<?php

namespace App\Providers;

use App\Models\EyeExamination;
use App\Models\Patient;
use App\Policies\EyeExaminationPolicy;
use App\Policies\PatientPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<string, string>
     */
    protected $policies = [
        Patient::class => PatientPolicy::class,
        EyeExamination::class => EyeExaminationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
