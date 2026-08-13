<?php

namespace App\Console\Commands;

use App\Domains\PosIntegration\SamplePosSyncService;
use Illuminate\Console\Command;

class PosDispatchReturnReports extends Command
{
    protected $signature = 'pos:dispatch-return-reports';

    protected $description = 'Dispatch pending WEB_RETURN_REPORT operations after sale ack';

    public function handle(SamplePosSyncService $sync): int
    {
        $count = $sync->dispatchPendingReturnReports();
        $this->info("Dispatched {$count} return report(s).");

        return self::SUCCESS;
    }
}
