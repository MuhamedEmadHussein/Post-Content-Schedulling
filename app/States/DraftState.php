<?php

namespace App\States;

use App\Models\Post;
use App\Models\ActivityLog;
use InvalidArgumentException;

class DraftState implements PostState
{
    public function schedule(Post $post, \DateTime $scheduledTime): void
    {
        if ($scheduledTime <= new \DateTime()) {
            throw new InvalidArgumentException('Scheduled time must be in the future');
        }
        $post->status = 'scheduled';
        $post->scheduled_time = $scheduledTime;
        $post->save();
        ActivityLog::create([
            'user_id' => $post->user_id,
            'action' => 'post_scheduled',
            'details' => "Post {$post->id} scheduled for {$scheduledTime->format('Y-m-d H:i:s')}",
        ]);
    }

    public function publish(Post $post): void
    {
        throw new InvalidArgumentException('Draft posts must be scheduled before publishing');
    }

    public function draft(Post $post): void
    {
        throw new InvalidArgumentException('Post is already in draft state');
    }
}