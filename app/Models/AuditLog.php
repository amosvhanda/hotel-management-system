<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = ['changes' => 'array', 'created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    /** Write a custom line, e.g. AuditLog::note($booking, 'Checked in'). */
    public static function note(Model $subject, string $description, array $changes = []): self
    {
        return static::create([
            'user_id'      => auth()->id(),
            'event'        => 'custom',
            'subject_type' => $subject->getMorphClass(),
            'subject_id'   => $subject->getKey(),
            'description'  => $description,
            'changes'      => $changes ?: null,
            'ip'           => request()?->ip(),
            'created_at'   => now(),
        ]);
    }
}
