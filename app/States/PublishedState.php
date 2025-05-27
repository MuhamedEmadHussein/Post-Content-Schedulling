<?php

namespace App\States;

use App\Models\Post;
use App\Models\ActivityLog;
use InvalidArgumentException;

class PublishedState implements PostState
{
    public function schedule(Post $post, \DateTime $scheduledTime): void
    {
        throw new InvalidArgumentException('Cannot schedule a published post');
    }

    public function publish(Post $post): void
    {
        throw new InvalidArgumentException('Post is already published');
    }

    public function draft(Post $post): void
    {
        $post->status = 'draft';
        $post->scheduled_time = null;
        $post->save();
        foreach ($post->platforms as $platform) {
            $post->platforms()->updateExistingPivot($platform->id, ['platform_status' => 'pending']);
        }
        ActivityLog::create([
            'user_id' => $post->user_id,
            'action' => 'post_reverted_to_draft',
            'details' => "Post {$post->id} reverted to draft from published",
        ]);
    }
}