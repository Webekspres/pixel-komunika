<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('orders:auto-cancel-unpaid')->dailyAt('00:05');

/*
| Scheduler stubs (SRS §11) — remaining jobs:
| - daily POS master/inventory sync
| - periodic stock reconciliation
| - sales/return report reconciliation
| - retry/reconciliation
| - temporary upload cleanup
| - log pruning
|
| Shared hosting: cron → `php artisan schedule:run`
| Queue: `php artisan queue:work --stop-when-empty` (bounded worker)
*/
