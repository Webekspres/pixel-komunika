<?php

namespace App\Console\Commands;

use App\Domains\PosIntegration\SamplePosSyncService;
use Illuminate\Console\Command;

class PosSyncMasters extends Command
{
    protected $signature = 'pos:sync-masters';

    protected $description = 'Sync POS master data via sample adapter';

    public function handle(SamplePosSyncService $sync): int
    {
        $run = $sync->syncMasters();
        $this->info("Master sync {$run->status} (run #{$run->id}).");

        return $run->status === 'FAILED' ? self::FAILURE : self::SUCCESS;
    }
}
