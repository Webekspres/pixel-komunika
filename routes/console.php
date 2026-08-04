<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Scheduler stubs (SRS §11) — implement when domain jobs exist:
| - daily POS master/inventory sync
| - periodic stock reconciliation
| - sales/return report reconciliation
| - auto-cancel WAITING_PAYMENT from previous calendar day
| - retry/reconciliation
| - temporary upload cleanup
| - log pruning
|
| Shared hosting: cron → `php artisan schedule:run`
| Queue: `php artisan queue:work --stop-when-empty` (bounded worker)
*/
