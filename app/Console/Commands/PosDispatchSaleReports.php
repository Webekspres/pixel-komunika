<?php

namespace App\Console\Commands;

use App\Domains\PosIntegration\SamplePosSyncService;
use Illuminate\Console\Command;

class PosDispatchSaleReports extends Command
{
    protected $signature = 'pos:dispatch-sale-reports';

    protected $description = 'Dispatch pending WEB_SALE_REPORT operations to sample POS ack simulator';

    public function handle(SamplePosSyncService $sync): int
    {
        $count = $sync->dispatchPendingSaleReports();
        $this->info("Dispatched {$count} sale report(s).");

        return self::SUCCESS;
    }
}
