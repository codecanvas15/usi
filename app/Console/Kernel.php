<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $is_local = config('app.env') === 'local';

        $website_domain = str_replace(['http://', 'https://'], '', config('app.url'));
        $use_supervisor = ['usi.soltek.id', 'pte.soltek.id', 'pps.soltek.id'];

        if ($is_local || !in_array($website_domain, $use_supervisor)) {
            $schedule->command('queue:work --stop-when-empty --timeout=0')->everyMinute()->withoutOverlapping(15 * 60);
        }
        $schedule->command('backup:run --only-db')->daily()->at('23:00');
        $schedule->command('backup:clean')->daily()->at('00:00');

        if ($is_local) {
            $schedule->job(new \App\Jobs\WeeklyRefreshStockJob())
                ->dailyAt(Carbon::now()->format('H:i'))
                ->withoutOverlapping(15 * 60);
        } else {
            $schedule->job(new \App\Jobs\WeeklyRefreshStockJob())
                ->weeklyOn(6, '00:00')
                ->withoutOverlapping(15 * 60);
        }

        // DailyRefreshStockJob previous month relative to execution time, exclude day 6
        $schedule->job(new \App\Jobs\DailyRefreshStockJob(Carbon::now()->subMonth(), null))
            ->dailyAt($is_local ? Carbon::now()->format('H:i') : '00:00')
            ->when(function () {
                return Carbon::now()->day !== 6;
            })
            ->withoutOverlapping(15 * 60);

        $schedule->job(new \App\Jobs\DailyRefreshStockJob(Carbon::now(), null))
            ->dailyAt($is_local ? Carbon::now()->format('H:i') : '00:00')
            ->when(function () {
                return Carbon::now()->day !== 6;
            })
            ->withoutOverlapping(15 * 60);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
