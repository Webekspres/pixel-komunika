<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('orders:auto-cancel-unpaid')->dailyAt('00:05');
Schedule::command('orders:auto-complete-shipped')->dailyAt('00:15');
Schedule::command('pos:sync-masters')->dailyAt('01:00');
Schedule::command('pos:sync-stock')->dailyAt('01:30');
Schedule::command('pos:dispatch-sale-reports')->everyFiveMinutes();
Schedule::command('pos:dispatch-return-reports')->everyFiveMinutes();
Schedule::command('notifications:dispatch-pending')->everyFiveMinutes();

/*
| Shared hosting: cron → `php artisan schedule:run`
| Queue: `php artisan queue:work --stop-when-empty` (bounded worker)
*/
