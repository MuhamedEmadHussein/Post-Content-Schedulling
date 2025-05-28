<?php

namespace App\Observers;

use App\Models\Platform;
use Illuminate\Support\Facades\Cache;

class PlatformObserver
{
    public function saved(Platform $platform): void
    {
        // Clear platforms cache
        Cache::forget('platforms');
    }
    /**
     * Handle the Platform "created" event.
     */
    public function created(Platform $platform): void
    {
        //
    }

    /**
     * Handle the Platform "updated" event.
     */
    public function updated(Platform $platform): void
    {
        //
    }

    /**
     * Handle the Platform "deleted" event.
     */
    public function deleted(Platform $platform): void
    {
        //
    }

    /**
     * Handle the Platform "restored" event.
     */
    public function restored(Platform $platform): void
    {
        //
    }

    /**
     * Handle the Platform "force deleted" event.
     */
    public function forceDeleted(Platform $platform): void
    {
        //
    }
}