<?php

namespace App\Providers;

use App\Repositories\Contracts\PatientRepositoryInterface;
use App\Repositories\Eloquent\PatientRepository;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoryServiceProvider — Binds repository interfaces to
 * their concrete Eloquent implementations.
 *
 * This enables dependency injection throughout the application:
 * type-hint the interface and Laravel resolves the Eloquent class.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository bindings for the application.
     *
     * Add new interface → implementation pairs here as the
     * application grows (e.g., AppointmentRepository, etc.).
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        PatientRepositoryInterface::class => PatientRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        // Bindings are handled declaratively via $bindings above.
    }
}
