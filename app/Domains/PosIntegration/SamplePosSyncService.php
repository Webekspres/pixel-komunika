<?php

namespace App\Domains\PosIntegration;

use App\Domains\SeedDataSupport\SampleCatalogImporter;
use App\Models\PosIntegrationOperation;
use App\Models\SalesReturn;
use App\Models\SyncError;
use App\Models\SyncRun;
use Illuminate\Support\Str;

/**
 * Sample/stub POS adapter until OPN-005/019 HTTP contract lands.
 */
class SamplePosSyncService implements PosMasterSyncInterface
{
    public function __construct(
        protected SampleCatalogImporter $catalogImporter,
        protected SamplePosAckSimulator $ackSimulator,
    ) {}

    public function syncMasters(): SyncRun
    {
        $run = SyncRun::query()->create([
            'sync_type' => 'MASTER_FULL',
            'source' => 'SEEDER',
            'status' => 'RUNNING',
            'started_at' => now(),
            'correlation_id' => (string) Str::uuid(),
            'created_at' => now(),
        ]);

        try {
            $this->catalogImporter->import();
            $run->update([
                'status' => 'SUCCEEDED',
                'finished_at' => now(),
                'success_count' => 1,
                'summary' => ['message' => 'Sample master sync via SampleCatalogImporter'],
            ]);
        } catch (\Throwable $e) {
            SyncError::query()->create([
                'sync_run_id' => $run->id,
                'entity_type' => 'catalog',
                'error_code' => 'SYNC_FAILED',
                'error_message' => $e->getMessage(),
                'retryable' => true,
                'created_at' => now(),
            ]);
            $run->update([
                'status' => 'FAILED',
                'finished_at' => now(),
                'failed_count' => 1,
            ]);
        }

        return $run->fresh();
    }

    public function syncStock(): SyncRun
    {
        // Same sample path; inventory is part of importer.
        $run = $this->syncMasters();
        $run->update(['sync_type' => 'INVENTORY_FULL']);

        return $run->fresh();
    }

    public function dispatchPendingSaleReports(int $limit = 50): int
    {
        return $this->dispatchPending(PosIntegrationOperation::WEB_SALE_REPORT, $limit);
    }

    public function dispatchPendingReturnReports(int $limit = 50): int
    {
        $ops = PosIntegrationOperation::query()
            ->where('operation', PosIntegrationOperation::WEB_RETURN_REPORT)
            ->where('status', PosIntegrationOperation::PENDING)
            ->limit($limit)
            ->get();

        $done = 0;
        foreach ($ops as $op) {
            $saleOk = PosIntegrationOperation::query()
                ->where('order_id', $op->order_id)
                ->where('operation', PosIntegrationOperation::WEB_SALE_REPORT)
                ->whereIn('status', [PosIntegrationOperation::SUCCEEDED, PosIntegrationOperation::RECONCILIATION_REQUIRED])
                ->exists();

            if (! $saleOk) {
                continue; // wait until sale ack
            }

            $this->applyAck($op);
            $done++;
        }

        return $done;
    }

    protected function dispatchPending(string $operation, int $limit): int
    {
        $ops = PosIntegrationOperation::query()
            ->where('operation', $operation)
            ->where('status', PosIntegrationOperation::PENDING)
            ->limit($limit)
            ->get();

        foreach ($ops as $op) {
            $this->applyAck($op);
        }

        return $ops->count();
    }

    protected function applyAck(PosIntegrationOperation $op): void
    {
        $result = $this->ackSimulator->simulate($op->external_reference);

        $op->update([
            'status' => $result['status'],
            'attempt_count' => $op->attempt_count + 1,
            'last_attempt_at' => now(),
            'response_reference' => $result['response_reference'],
            'response_payload_redacted' => $result['payload'],
            'error_code' => $result['error_code'],
            'error_message' => $result['error_message'],
            'reconciled_at' => $result['status'] === PosIntegrationOperation::SUCCEEDED ? now() : null,
        ]);

        if ($op->operation === PosIntegrationOperation::WEB_RETURN_REPORT && $op->order_id) {
            SalesReturn::query()->where('order_id', $op->order_id)->update([
                'reporting_status' => match ($result['status']) {
                    PosIntegrationOperation::SUCCEEDED => SalesReturn::SUCCEEDED,
                    PosIntegrationOperation::FAILED => SalesReturn::FAILED,
                    default => SalesReturn::RECONCILIATION_REQUIRED,
                },
                'pos_ack_reference' => $result['response_reference'],
                'reported_at' => $result['status'] === PosIntegrationOperation::SUCCEEDED ? now() : null,
            ]);
        }
    }
}
