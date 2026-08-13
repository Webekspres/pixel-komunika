<?php

namespace App\Domains\PosIntegration;

use App\Models\PosIntegrationOperation;

/**
 * Deterministic ack simulator for sample POS (MVP §4).
 * Suffix of external_reference can force outcome: :fail | :timeout
 */
class SamplePosAckSimulator
{
    public function simulate(string $externalReference): array
    {
        if (str_ends_with($externalReference, ':fail')) {
            return [
                'status' => PosIntegrationOperation::FAILED,
                'response_reference' => null,
                'payload' => ['ok' => false],
                'error_code' => 'POS_REJECTED',
                'error_message' => 'Simulated POS rejection',
            ];
        }

        if (str_ends_with($externalReference, ':timeout')) {
            return [
                'status' => PosIntegrationOperation::AMBIGUOUS,
                'response_reference' => null,
                'payload' => ['ok' => null, 'timeout' => true],
                'error_code' => 'POS_TIMEOUT',
                'error_message' => 'Simulated ambiguous timeout',
            ];
        }

        return [
            'status' => PosIntegrationOperation::SUCCEEDED,
            'response_reference' => 'POS-ACK-'.substr(hash('sha256', $externalReference), 0, 12),
            'payload' => ['ok' => true],
            'error_code' => null,
            'error_message' => null,
        ];
    }
}
