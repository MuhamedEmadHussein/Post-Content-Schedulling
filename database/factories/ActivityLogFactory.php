<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['post_created', 'post_updated', 'post_scheduled', 'post_published', 'post_reverted_to_draft']),
            'details' => $this->faker->sentence,
            'created_at' => $this->faker->dateTimeThisMonth(),
        ];
    }
}