<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AdminSeeder — Creates the default system admin account.
 *
 * This is a single fixed account used to manage the system.
 * Login: admin@eyeai.local / password
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@eyeai.local'],
            [
                'name'              => 'System Admin',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
