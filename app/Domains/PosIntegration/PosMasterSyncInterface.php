<?php

namespace App\Domains\PosIntegration;

use App\Models\SyncRun;

interface PosMasterSyncInterface
{
    public function syncMasters(): SyncRun;
}
