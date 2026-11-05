<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'title',
        'description',
        'icon_class',
        'image',
    ];

    /** Short list so staff pick an icon instead of typing a class name. */
    public const ICON_OPTIONS = [
        'bx bx-restaurant' => 'Restaurant',
        'bx bx-car' => 'Airport transfer',
        'bx bx-chalkboard' => 'Conference',
        'bx bx-wifi' => 'Wi-Fi',
        'bx bx-closet' => 'Laundry',
        'bx bx-swim' => 'Pool',
        'bx bx-dumbbell' => 'Gym',
        'bx bx-spa' => 'Spa',
        'bx bx-bed' => 'Rooms',
        'bx bx-coffee' => 'Breakfast',
    ];

    /** Boxicon class for the card. Uses the saved choice, then the title. */
    public function getIconAttribute(): string
    {
        $stored = trim((string) $this->icon_class);
        if (str_starts_with($stored, 'bx ') || str_starts_with($stored, 'bxs ')) {
            return $stored;
        }

        $title = strtolower((string) $this->title);
        $map = [
            'bx bx-restaurant' => ['restaurant', 'dining', 'food', 'bar', 'breakfast'],
            'bx bx-car' => ['airport', 'transfer', 'shuttle', 'taxi'],
            'bx bx-chalkboard' => ['conference', 'meeting', 'event', 'workshop'],
            'bx bx-wifi' => ['wifi', 'wi-fi', 'internet'],
            'bx bx-closet' => ['laundry', 'dry clean', 'ironing'],
            'bx bx-swim' => ['pool', 'swim'],
            'bx bx-dumbbell' => ['gym', 'fitness'],
            'bx bx-spa' => ['spa', 'massage', 'sauna'],
            'bx bx-coffee' => ['coffee', 'tea'],
        ];
        foreach ($map as $icon => $words) {
            foreach ($words as $word) {
                if (str_contains($title, $word)) {
                    return $icon;
                }
            }
        }

        return 'bx bx-hotel';
    }

    /**
     * Image to show on the website: the uploaded photo, or a matching built-in illustration.
     */
    public function getDisplayImageAttribute(): string
    {
        if ($this->image && is_file(public_path($this->image))) {
            return asset($this->image);
        }

        $title = strtolower((string) $this->title);
        $body = strtolower((string) $this->description);
        $map = [
            'dining'      => ['restaurant', 'dining', 'food', 'breakfast', 'dinner', 'lunch', 'bar', 'cafe', 'meal', 'kitchen'],
            'transfer'    => ['airport', 'transfer', 'shuttle', 'taxi', 'car', 'transport', 'parking', 'pick'],
            'spa'         => ['spa', 'massage', 'wellness', 'sauna', 'beauty', 'relax'],
            'wifi'        => ['wifi', 'wi-fi', 'internet', 'wireless'],
            'laundry'     => ['laundry', 'dry clean', 'ironing', 'washing'],
            'conference'  => ['conference', 'meeting', 'event', 'business', 'workshop', 'wedding'],
            'pool'        => ['pool', 'swim'],
            'gym'         => ['gym', 'fitness', 'workout', 'exercise'],
            'roomservice' => ['room service', 'housekeeping', 'concierge', '24/7', 'butler'],
        ];

        // The title decides first; the description is only a fallback.
        foreach ([$title, $body] as $text) {
            foreach ($map as $file => $words) {
                foreach ($words as $word) {
                    if (str_contains($text, $word)) {
                        return asset("frontend/assets/img/services/{$file}.jpg");
                    }
                }
            }
        }

        return asset('frontend/assets/img/services/default.jpg');
    }
}
