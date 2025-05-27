<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function getUserPosts(int $userId, array $filters = []): Collection;
    public function create(array $data): Post;
    public function update(int $postId, array $data): Post;
    public function delete(int $postId): bool;
    public function getDuePosts(): Collection;
}