<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Audit
{
    /** @param array<string, mixed> $metadata */
    public static function record(string $action, Model $model, ?int $actorId, array $metadata = []): void
    {
        AuditLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'metadata' => $metadata ?: null,
            'correlation_id' => request()->attributes->get('correlation_id', (string) Str::uuid()),
        ]);
    }
}
