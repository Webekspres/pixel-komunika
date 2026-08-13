<?php

namespace App\Domains\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLogger
{
    public function log(
        string $action,
        Model $auditable,
        ?User $actor = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        return AuditLog::query()->create([
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'request_id' => request()->headers->get('X-Request-Id'),
            'ip_address' => request()->ip(),
            'user_agent' => ($ua = request()->userAgent()) ? Str::limit($ua, 500, '') : null,
            'created_at' => now(),
        ]);
    }
}
