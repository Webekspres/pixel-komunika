<?php

namespace App\Console\Commands;

use App\Domains\PosIntegration\PosMasterSyncInterface;
use App\Domains\PosIntegration\SamplePosSyncService;
use App\Domains\PosIntegration\SandboxPosMasterSyncService;
use Illuminate\Console\Command;

class PosSyncMasters extends Command
{
    protected $signature = 'pos:sync-masters {--driver= : Specific POS driver to use (sample, sandbox)}';

    protected $description = 'Sync POS master data via configured driver';

    public function handle(): int
    {
        $driver = $this->option('driver') ?: config('pos.driver', 'sample');

        /** @var PosMasterSyncInterface $sync */
        $sync = match ($driver) {
            'sandbox' => app(SandboxPosMasterSyncService::class),
            default => app(SamplePosSyncService::class),
        };

        $run = $sync->syncMasters();
        $this->info("Master sync {$run->status} (run #{$run->id}) using driver [{$driver}].");

        return $run->status === 'FAILED' ? self::FAILURE : self::SUCCESS;
    }
}
