<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Platform;
use App\Models\PostPlatform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostPlatformFactory extends Factory
{
    protected $model = PostPlatform::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'platform_id' => Platform::factory(),
            'platform_status' => $this->faker->randomElement(['pending', 'published']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}