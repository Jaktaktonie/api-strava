<?php

namespace Database\Seeders;

use App\Models\Activity;
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

        $admin = User::updateOrCreate(
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

        $tester = User::updateOrCreate(
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

        $sampleUsers = User::factory()->count(3)->create();

        $seededUsers = collect([$admin, $tester])->filter()->merge($sampleUsers);

        $seededUsers->each(function (User $user): void {
            Activity::factory()->count(2)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
