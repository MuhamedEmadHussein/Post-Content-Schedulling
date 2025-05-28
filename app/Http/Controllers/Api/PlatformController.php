<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlatformResource;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Cache::remember('platforms', 3600, fn() => Platform::all());
        return PlatformResource::collection($platforms);
    }

    public function userPlatforms()
    {
        $platforms = Auth::user()->platforms;
        return PlatformResource::collection($platforms);
    }

    public function updateUserPlatforms(Request $request)
    {
        $request->validate([
            'platform_ids' => 'required|array',
            'platform_ids.*' => 'exists:platforms,id',
        ]);

        Auth::user()->platforms()->sync($request->platform_ids);
        return response()->json(['message' => 'Platforms updated']);
    }
}