<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        \App\Console\Commands\Notifications::class,
        \App\Console\Commands\BirthdayWishes::class,
        \App\Console\Commands\LowQuantityProduct::class,
        \App\Console\Commands\OrderDeliveryDay::class,
        \App\Console\Commands\ExpirationOfPromoCodes::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('send-notifications')->everyTwoMinutes();

        // Birthday wishes
        // $schedule->command('birthdaywishes')->monthlyOn(1, '1:00');

        // Low quantity product
        // $schedule->command('lowquantityproduct')->dailyAt('9:00');

        // Order delivery day
        // $schedule->command('orderdeliveryday')->dailyAt('8:00');

        // Expiration of promo codes
        $schedule->command('expirationofpromocodes')->dailyAt('1:00');
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
