<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['run', 'ride', 'walk']);
        $start = fake()->dateTimeBetween('-1 month', 'now');
        $duration = fake()->numberBetween(1200, 7200); // seconds
        $end = (clone $start)->modify("+{$duration} seconds");
        $distance = fake()->numberBetween(2000, 40000); // meters
        $avgSpeed = round(($distance / 1000) / ($duration / 3600), 2);
        $avgPace = round(($duration / 60) / max(0.1, ($distance / 1000)), 2);

        return [
            'user_id' => User::factory(),
            'title' => ucfirst($type).' '.fake()->words(2, true),
            'type' => $type,
            'start_time' => $start,
            'end_time' => $end,
            'duration_seconds' => $duration,
            'distance_meters' => $distance,
            'avg_speed_kmh' => $avgSpeed,
            'avg_pace' => $avgPace,
            'route' => collect(range(1, 5))->map(fn () => [
                'lat' => fake()->latitude(),
                'lng' => fake()->longitude(),
            ])->all(),
            'notes' => fake()->sentence(),
        ];
    }
}
