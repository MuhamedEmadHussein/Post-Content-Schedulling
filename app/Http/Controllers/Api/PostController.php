<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\SchedulePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Services\PostService;
use App\States\PostState;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\States\DraftState;
use App\States\ScheduledState;
use App\States\PublishedState;
class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'date']);
        $posts = $this->postService->getUserPosts(Auth::id(), $filters);
        return new PostCollection($posts);
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->createPost($request->validated());
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $post = $this->postService->updatePost($id, $request->validated());
        return new PostResource($post);
    }

    public function destroy($id)
    {
        $this->postService->delete($id);
        return response()->json(null, 204);
    }

    public function schedule(SchedulePostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        $state = $this->getState($post);
        $state->schedule($post, new \DateTime($request->input('scheduled_time')));
        return new PostResource($post);
    }

    public function publish($id)
    {
        $post = Post::findOrFail($id);
        $state = $this->getState($post);
        $state->publish($post);
        return new PostResource($post);
    }

    public function draft($id)
    {
        $post = Post::findOrFail($id);
        $state = $this->getState($post);
        $state->draft($post);
        return new PostResource($post);
    }

    protected function getState(Post $post): PostState
    {
        return match ($post->status) {
            'draft' => new DraftState(),
            'scheduled' => new ScheduledState(),
            'published' => new PublishedState(),
            default => throw new \Exception('Invalid post status'),
        };
    }
}