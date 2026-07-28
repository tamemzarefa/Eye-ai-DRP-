<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

/**
 * PatientSeeder — Creates demo patient records.
 *
 * Seeds 20 realistic patients for development and testing.
 */
class PatientSeeder extends Seeder
{
    public function run(): void
    {
        Patient::factory(20)->create();
    }
}
