<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Records created / updated / deleted with the changed fields and who did it.
 * Never breaks the save if logging fails.
 */
trait Auditable
{
    /** Fields never written to the audit log. */
    protected static array $auditIgnore = ['updated_at', 'created_at', 'password', 'remember_token'];

    public static function bootAuditable(): void
    {
        static::created(fn (Model $m) => static::writeAudit($m, 'created', array_map(fn ($v) => [null, $v], static::auditable($m->getAttributes()))));
        static::updated(function (Model $m) {
            $changes = [];
            foreach (static::auditable($m->getChanges()) as $field => $new) {
                $changes[$field] = [$m->getOriginal($field), $new];
            }
            if ($changes) {
                static::writeAudit($m, 'updated', $changes);
            }
        });
        static::deleted(fn (Model $m) => static::writeAudit($m, 'deleted', []));
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'subject')->latest('id');
    }

    protected static function auditable(array $attributes): array
    {
        return array_diff_key($attributes, array_flip(static::$auditIgnore));
    }

    protected static function writeAudit(Model $m, string $event, array $changes): void
    {
        try {
            AuditLog::create([
                'user_id'      => auth()->id(),
                'event'        => $event,
                'subject_type' => $m->getMorphClass(),
                'subject_id'   => $m->getKey(),
                'changes'      => $changes ?: null,
                'ip'           => app()->runningInConsole() ? null : request()->ip(),
                'created_at'   => now(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
