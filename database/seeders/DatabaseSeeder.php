<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityComment;
use App\Models\ActivityLike;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

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

        $samplePassword = env('SAMPLE_USER_PASSWORD', 'Sample123!');

        $seedUsersData = [
            [
                'first_name' => 'Ala',
                'last_name' => 'Runner',
                'email' => 'ala@ministrava.dev',
                'locale' => 'pl',
                'timezone' => 'Europe/Warsaw',
            ],
            [
                'first_name' => 'Bartek',
                'last_name' => 'Cyclist',
                'email' => 'bartek@ministrava.dev',
                'locale' => 'pl',
                'timezone' => 'Europe/Warsaw',
            ],
            [
                'first_name' => 'Cleo',
                'last_name' => 'Walker',
                'email' => 'cleo@ministrava.dev',
                'locale' => 'en',
                'timezone' => 'Europe/London',
            ],
            [
                'first_name' => 'Dawid',
                'last_name' => 'Sprinter',
                'email' => 'dawid@ministrava.dev',
                'locale' => 'pl',
                'timezone' => 'Europe/Warsaw',
            ],
            [
                'first_name' => 'Ewa',
                'last_name' => 'Hiker',
                'email' => 'ewa@ministrava.dev',
                'locale' => 'en',
                'timezone' => 'Europe/Paris',
            ],
        ];

        $sampleUsers = collect($seedUsersData)->map(function (array $data) use ($samplePassword) {
            $fullName = "{$data['first_name']} {$data['last_name']}";

            return User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'name' => $fullName,
                    'locale' => $data['locale'],
                    'timezone' => $data['timezone'],
                    'password' => Hash::make($samplePassword),
                    'email_verified_at' => Carbon::now(),
                    'remember_token' => Str::random(10),
                ]
            );
        });

        $seededUsers = collect([$admin, $tester])->filter()->merge($sampleUsers);

        // Tworzymy aktywności dla każdego usera.
        $userActivities = $seededUsers->mapWithKeys(function (User $user) {
            $activities = Activity::factory()->count(2)->create([
                'user_id' => $user->id,
            ]);

            return [$user->id => $activities];
        });

        // Interakcje społecznościowe między pięcioma użytkownikami demo.
        $demoUsers = $sampleUsers->values();
        if ($demoUsers->count() >= 5) {
            [$u1, $u2, $u3, $u4, $u5] = $demoUsers->take(5);

            // Przyjaźnie akceptowane.
            FriendRequest::updateOrCreate(
                ['sender_id' => $u1->id, 'receiver_id' => $u2->id],
                ['status' => 'accepted']
            );
            FriendRequest::updateOrCreate(
                ['sender_id' => $u3->id, 'receiver_id' => $u4->id],
                ['status' => 'accepted']
            );
            FriendRequest::updateOrCreate(
                ['sender_id' => $u2->id, 'receiver_id' => $u5->id],
                ['status' => 'accepted']
            );

            // Jeden przykład blokady (u5 blokuje u1).
            UserBlock::firstOrCreate([
                'blocker_id' => $u5->id,
                'blocked_id' => $u1->id,
            ]);

            // Likes i komentarze na wybranych aktywnościach.
            $likeTargets = collect([
                $userActivities[$u1->id][0] ?? null,
                $userActivities[$u2->id][0] ?? null,
                $userActivities[$u3->id][0] ?? null,
            ])->filter();

            foreach ($likeTargets as $activity) {
                foreach ([$u2, $u3, $u4] as $fan) {
                    ActivityLike::firstOrCreate([
                        'activity_id' => $activity->id,
                        'user_id' => $fan->id,
                    ]);
                }

                ActivityComment::create([
                    'activity_id' => $activity->id,
                    'user_id' => $u4->id,
                    'content' => 'Świetny trening!',
                ]);

                ActivityComment::create([
                    'activity_id' => $activity->id,
                    'user_id' => $u3->id,
                    'content' => 'Jakie tempo, brawo!',
                ]);
            }
        }
    }
}
