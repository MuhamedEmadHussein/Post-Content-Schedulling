<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Platform;
use App\Models\User;
use App\Observers\PostObserver;
use App\Observers\PlatformObserver;
use App\Observers\UserObserver;
use App\Interfaces\PostRepositoryInterface;
use App\Repositories\PostRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Post::observe(PostObserver::class);
        Platform::observe(PlatformObserver::class);
        User::observe(UserObserver::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    }
}