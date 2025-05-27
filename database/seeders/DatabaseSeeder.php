<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Platform;
use App\Models\PostPlatform;
use App\Models\UserPlatform;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PlatformSeeder::class);

        $users = User::factory()->count(5)->create();

        $platforms = Platform::all();
        foreach ($users as $user) {
            $platformCount = min($platforms->count(), rand(2, 3));
            $userPlatforms = $platforms->random($platformCount)->pluck('id');
            $user->platforms()->sync($userPlatforms);

            $posts = Post::factory()->count(rand(1, 5))->create(['user_id' => $user->id]);
            foreach ($posts as $post) {
                $maxPlatforms = min($userPlatforms->count(), 2);
                $postPlatformCount = rand(1, $maxPlatforms);
                $postPlatforms = $userPlatforms->random($postPlatformCount);
                $post->platforms()->sync($postPlatforms->mapWithKeys(function ($platformId) {
                    return [$platformId => ['platform_status' => rand(0, 1) ? 'pending' : 'published']];
                }));

                ActivityLog::factory()->create([
                    'user_id' => $user->id,
                    'action' => 'post_created',
                    'details' => "Post {$post->id} created for platforms: " . $post->platforms->pluck('name')->implode(', '),
                ]);
            }

            ActivityLog::factory()->count(3)->create(['user_id' => $user->id]);
        }
    }
}