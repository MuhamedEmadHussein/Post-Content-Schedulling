<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use App\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    public function getUserPosts(int $userId, array $filters = [])
    {
        $query = Post::where('user_id', $userId)->with('platforms');
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['date'])) {
            $query->whereDate('scheduled_time', $filters['date']);
        }
        return $query->orderBy('scheduled_time', 'desc')->paginate(10);
    }

    public function create(array $data): Post
    {
        $post = Post::create($data);
        $post->platforms()->sync($data['platforms']);
        return $post;
    }

    public function update(int $postId, array $data): Post
    {
        $post = Post::findOrFail($postId);
        $post->update($data);
        if (isset($data['platforms'])) {
            $post->platforms()->sync($data['platforms']);
        }
        return $post;
    }

    public function delete(int $postId): bool
    {
        return Post::destroy($postId) > 0;
    }

    public function getDuePosts(): Collection
    {
        return Cache::remember('due_posts', 60, fn() => Post::where('scheduled_time', '<=', now())
            ->where('status', 'scheduled')
            ->with('platforms')
            ->get());
    }
}