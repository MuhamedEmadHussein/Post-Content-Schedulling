<?php

namespace App\Services;

use App\Interfaces\PostRepositoryInterface;
use App\Models\ActivityLog;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function createPost(array $data): Post
    {
        $this->validatePlatforms($data['platforms'], $data['content']);
        $this->enforceRateLimit();
        $data['user_id'] = Auth::id();
        $post = $this->postRepository->create($data);
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'post_created',
            'details' => "Post {$post->id} created for platforms: " . implode(', ', $data['platforms']),
        ]);
        return $post;
    }

    public function updatePost(int $postId, array $data): Post
    {
        $post = Post::findOrFail($postId);
        if ($post->status === 'published') {
            throw new \Exception('Cannot update published posts');
        }
        $this->validatePlatforms($data['platforms'] ?? $post->platforms->pluck('id')->toArray(), $data['content'] ?? $post->content);
        $post = $this->postRepository->update($postId, $data);
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'post_updated',
            'details' => "Post {$postId} updated",
        ]);
        return $post;
    }

    public function enforceRateLimit(): void
    {
        $count = Cache::remember("user:{Auth::id()}:post_count:" . now()->toDateString(), 86400, fn() =>
            Post::where('user_id', Auth::id())
                ->where('status', 'scheduled')
                ->whereDate('created_at', now()->toDateString())
                ->count()
        );
        if ($count >= 10) {
            throw new \Exception('Daily scheduled post limit reached (10 posts)');
        }
    }

    protected function validatePlatforms(array $platformIds, string $content): void
    {
        $platforms = Cache::remember('platforms', 3600, fn() => \App\Models\Platform::whereIn('id', $platformIds)->get());
        foreach ($platforms as $platform) {
            $limit = match ($platform->type) {
                'x' => 280,
                'instagram' => 2200,
                'linkedin' => 3000,
                default => 1000,
            };
            if (strlen($content) > $limit) {
                throw new \Exception("Content exceeds {$platform->type} character limit of {$limit}");
            }
        }
    }

    public function getUserPosts(int $userId, array $filters = [])
    {
        return $this->postRepository->getUserPosts($userId, $filters);
    }

    public function delete(int $postId)
    {
        $this->postRepository->delete($postId);
    }

    public function getDuePosts()
    {
        return $this->postRepository->getDuePosts();
    }
}