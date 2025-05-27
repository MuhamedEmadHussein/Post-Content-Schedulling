<?php

namespace App\States;

use App\Models\Post;

interface PostState
{
    public function schedule(Post $post, \DateTime $scheduledTime): void;
    public function publish(Post $post): void;
    public function draft(Post $post): void;
}