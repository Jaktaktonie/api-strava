<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@ministrava.dev');
        $adminPassword = env('ADMIN_PASSWORD', 'Admin123!');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'first_name' => 'MiniStrava',
                'last_name' => 'Admin',
                'name' => 'MiniStrava Admin',
                'role' => 'admin',
                'locale' => 'pl',
                'timezone' => 'Europe/Warsaw',
                'password' => Hash::make($adminPassword),
            ]
        );

        $testerEmail = env('TEST_USER_EMAIL', 'tester@ministrava.dev');
        $testerPassword = env('TEST_USER_PASSWORD', 'User123!');

        User::updateOrCreate(
            ['email' => $testerEmail],
            [
                'first_name' => 'Test',
                'last_name' => 'Athlete',
                'name' => 'Test Athlete',
                'locale' => 'en',
                'timezone' => 'Europe/Warsaw',
                'password' => Hash::make($testerPassword),
            ]
        );
    }
}
