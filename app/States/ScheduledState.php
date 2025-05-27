<?php

namespace App\States;

use App\Models\Post;
use App\Models\ActivityLog;
use InvalidArgumentException;

class ScheduledState implements PostState
{
    public function schedule(Post $post, \DateTime $scheduledTime): void
    {
        if ($scheduledTime <= new \DateTime()) {
            throw new InvalidArgumentException('Scheduled time must be in the future');
        }
        $oldTime = $post->scheduled_time->format('Y-m-d H:i:s');
        $post->scheduled_time = $scheduledTime;
        $post->save();
        ActivityLog::create([
            'user_id' => $post->user_id,
            'action' => 'post_rescheduled',
            'details' => "Post {$post->id} rescheduled from {$oldTime} to {$scheduledTime->format('Y-m-d H:i:s')}",
        ]);
    }

    public function publish(Post $post): void
    {
        $post->status = 'published';
        $post->save();
        foreach ($post->platforms as $platform) {
            $post->platforms()->updateExistingPivot($platform->id, ['platform_status' => 'published']);
        }
        ActivityLog::create([
            'user_id' => $post->user_id,
            'action' => 'post_published',
            'details' => "Post {$post->id} published on " . $post->platforms->pluck('name')->implode(', '),
        ]);
    }

    public function draft(Post $post): void
    {
        $post->status = 'draft';
        $post->scheduled_time = null;
        $post->save();
        ActivityLog::create([
            'user_id' => $post->user_id,
            'action' => 'post_reverted_to_draft',
            'details' => "Post {$post->id} reverted to draft",
        ]);
    }
}