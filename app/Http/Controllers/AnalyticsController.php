<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $postsPerPlatform = Post::select('platforms.name', DB::raw('count(*) as total'))
            ->join('post_platform', 'posts.id', '=', 'post_platform.post_id')
            ->join('platforms', 'post_platform.platform_id', '=', 'platforms.id')
            ->where('posts.user_id', Auth::id())
            ->groupBy('platforms.name')
            ->get();

        $totalPosts = Post::where('user_id', Auth::id())->count();
        $successRate = $totalPosts ? Post::where('user_id', Auth::id())
            ->where('status', 'published')
            ->count() / $totalPosts : 0;

        return response()->json([
            'posts_per_platform' => $postsPerPlatform,
            'success_rate' => $successRate,
            'scheduled_vs_published' => [
                'scheduled' => Post::where('user_id', Auth::id())->where('status', 'scheduled')->count(),
                'published' => Post::where('user_id', Auth::id())->where('status', 'published')->count(),
            ],
        ]);
    }
}