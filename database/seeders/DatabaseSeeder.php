<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters:
     *  1. AdminSeeder  — system admin account
     *  2. DoctorSeeder — default demo doctor
     *  3. PatientSeeder — demo patients for development
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            DoctorSeeder::class,
            PatientSeeder::class,
        ]);
    }
}
