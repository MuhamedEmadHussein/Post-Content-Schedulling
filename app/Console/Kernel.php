<?php

namespace App\Console;

use App\Console\Commands\PublishPosts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        PublishPosts::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('publish:posts')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}