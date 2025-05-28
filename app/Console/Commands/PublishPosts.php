<?php

namespace App\Console\Commands;

use App\Services\PostService;
use Illuminate\Console\Command;

class PublishPosts extends Command
{
    protected $signature = 'publish:posts';
    protected $description = 'Publish scheduled posts that are due';

    protected $postService;

    public function __construct(PostService $postService)
    {
        parent::__construct();
        $this->postService = $postService;
    }

    public function handle()
    {
        $duePosts = $this->postService->getDuePosts();

        if ($duePosts->isEmpty()) {
            $this->info('No posts are due for publishing.');
            return 0;
        }

        foreach ($duePosts as $post) {
            try {
                $post->update(['status' => 'published']);
                \App\Models\ActivityLog::create([
                    'user_id' => $post->user_id,
                    'action' => 'post_published',
                    'details' => "Post {$post->id} published to platforms: " . $post->platforms->pluck('name')->implode(', '),
                ]);
                \Illuminate\Support\Facades\Cache::forget('due_posts');
                $this->info("Published post {$post->id}");
            } catch (\Exception $e) {
                $this->error("Failed to publish post {$post->id}: {$e->getMessage()}");
            }
        }

        return 0;
    }
}