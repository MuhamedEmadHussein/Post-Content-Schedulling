<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\ActivityLog;
use Illuminate\Console\Command;

class PublishPosts extends Command
{
    protected $signature = 'publish:posts';
    protected $description = 'Publish scheduled posts that are due';

    public function handle()
    {
        $duePosts = Post::where('scheduled_time', '<=', now())
            ->where('status', 'scheduled')
            ->with('platforms')
            ->get();

        foreach ($duePosts as $post) {
            $post->status = 'published';
            $post->save();
            foreach ($post->platforms as $platform) {
                $post->platforms()->updateExistingPivot($platform->id, ['platform_status' => 'published']);
            }
            ActivityLog::create([
                'user_id' => $post->user_id,
                'action' => 'post_published',
                'details' => "Post {$post->id} published on {$post->platforms->pluck('name')->implode(', ')}",
            ]);
        }

        $this->info('Due posts published successfully.');
    }
}