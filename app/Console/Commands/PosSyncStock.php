<?php

namespace App\Console\Commands;

use App\Domains\PosIntegration\SamplePosSyncService;
use Illuminate\Console\Command;

class PosSyncStock extends Command
{
    protected $signature = 'pos:sync-stock';

    protected $description = 'Sync POS inventory via sample adapter';

    public function handle(SamplePosSyncService $sync): int
    {
        $run = $sync->syncStock();
        $this->info("Stock sync {$run->status} (run #{$run->id}).");

        return $run->status === 'FAILED' ? self::FAILURE : self::SUCCESS;
    }
}
