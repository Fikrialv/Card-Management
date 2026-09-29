<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

final class AuditLog extends Model
{
    protected $fillable = ['actor_id', 'action', 'auditable_type', 'auditable_id', 'metadata', 'correlation_id'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    protected static function booted(): void
    {
        self::updating(fn () => throw new LogicException('Audit logs are immutable.'));
        self::deleting(fn () => throw new LogicException('Audit logs are immutable.'));
    }

    /** @return BelongsTo<User, $this> */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
