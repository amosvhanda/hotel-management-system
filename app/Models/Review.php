<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A guest's review of their stay. Only approved reviews are shown on the website. */
class Review extends Model
{
    protected $guarded = [];

    protected $casts = ['approved_at' => 'datetime'];

    public const PENDING = 0;
    public const APPROVED = 1;
    public const HIDDEN = 2;

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'rooms_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::APPROVED);
    }

    public function statusLabel(): string
    {
        return [self::PENDING => 'Waiting for approval', self::APPROVED => 'Published', self::HIDDEN => 'Hidden'][(int) $this->status] ?? 'Unknown';
    }

    /** First name + last initial, e.g. "Farai M." — what the public sees. */
    public function guestDisplayName(): string
    {
        $parts = preg_split('/\s+/', trim((string) ($this->user->name ?? $this->booking->name ?? 'Guest')));
        $first = $parts[0] ?? 'Guest';
        $last = count($parts) > 1 ? mb_substr(end($parts), 0, 1) . '.' : '';

        return trim("{$first} {$last}");
    }
}
