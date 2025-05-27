<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Platform;
use App\Models\UserPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPlatformFactory extends Factory
{
    protected $model = UserPlatform::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'platform_id' => Platform::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}