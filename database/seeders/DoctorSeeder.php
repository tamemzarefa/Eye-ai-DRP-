<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DoctorSeeder — Creates a default demo doctor account.
 *
 * Login: doctor@eyeai.local / password
 */
class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'doctor@eyeai.local'],
            [
                'name'              => 'Dr. Ahmed Al-Rashid',
                'password'          => Hash::make('password'),
                'role'              => 'doctor',
                'email_verified_at' => now(),
            ]
        );
    }
}
