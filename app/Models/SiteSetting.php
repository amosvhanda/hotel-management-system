<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'homepage' => 'array',
        'theme' => 'array',
        'maintenance_mode' => 'boolean',
        'social_sharing_enabled' => 'boolean',
    ];

    /** Copy shown until the hotel types its own in Homepage settings. */
    public const HOME_DEFAULTS = [
        'hero_kicker' => 'Harare',
        'hero_title' => 'A quiet room in the middle of Harare.',
        'hero_text' => 'Comfortable rooms on Samora Machel Avenue, a kitchen that cooks sadza as well as steak, and a booking you can pay with EcoCash, OneMoney, or when you arrive.',
        'booking_note' => 'Pay at the hotel, or with EcoCash and OneMoney. No booking fee.',
        'rooms_kicker' => 'Rooms',
        'rooms_title' => 'Rooms for a night, a week, or the conference',
        'moments_kicker' => 'The hotel',
        'moments_title' => 'Gardens, a long table, and the city just outside',
        'services_kicker' => 'While you are here',
        'services_title' => 'The rest of the stay, when you want it',
        'team_kicker' => 'The house team',
        'team_title' => 'People who will know your name',
        'guests_kicker' => 'Guests',
        'guests_title' => 'What people remember after they leave',
        'faq_kicker' => 'Before you arrive',
        'faq_title' => 'A few answers, so you can book without guessing',
        'faq_intro' => 'If your question is not here, call the front desk. A person will pick up.',
        'journal_kicker' => 'From Harare',
        'journal_title' => 'Notes for the weekend',
        'about_kicker' => 'Who we are',
        'about_title' => 'A calm place to rest, close to everything you need',
        'about_text' => 'Comfortable, well-kept rooms, friendly staff and simple online booking.',
        'about_point_1' => 'Clean, comfortable rooms with the essentials you expect',
        'about_point_2' => 'Book online in minutes. Pay at the hotel, or with EcoCash and OneMoney.',
        'about_point_3' => 'A front desk that answers, day and night',
        'about_why_kicker' => 'Why stay',
        'about_why_title' => 'Comfort, clear prices, and a desk that knows your name',
        'about_feature_1_title' => 'Quiet rooms',
        'about_feature_1_text' => 'Rooms set back from the street, so you can sleep after a day in the city.',
        'about_feature_2_title' => 'A price you can trust',
        'about_feature_2_text' => 'The rate on the room is the rate you pay. No booking fee.',
        'about_feature_3_title' => 'People at the desk',
        'about_feature_3_text' => 'Call, write, or walk in. Someone at the front desk will help.',
        'about_cta_title' => 'Ready when you are',
        'about_cta_text' => 'Check which rooms are free, then book online.',
        'contact_kicker' => 'The front desk',
        'contact_title' => 'Write, call, or come by.',
        'contact_text' => 'The desk is open day and night. Send a note here, or call if you need a room tonight.',
        'contact_hours' => 'Day and night',
        'rooms_page_kicker' => 'Rooms',
        'rooms_page_title' => 'Choose the room, then the dates',
        'gallery_kicker' => 'The hotel',
        'gallery_title' => 'Photographs of the house',
        'footer_plan' => 'Check availability and book online in a few clicks, or call the front desk.',
    ];

    public const HOME_TEXT_KEYS = [
        'hero_kicker', 'hero_title', 'hero_text', 'booking_note',
        'rooms_kicker', 'rooms_title',
        'moments_kicker', 'moments_title',
        'services_kicker', 'services_title',
        'team_kicker', 'team_title',
        'guests_kicker', 'guests_title',
        'faq_kicker', 'faq_title', 'faq_intro',
        'journal_kicker', 'journal_title',
        'about_kicker', 'about_title', 'about_text',
        'about_point_1', 'about_point_2', 'about_point_3',
        'about_why_kicker', 'about_why_title',
        'about_feature_1_title', 'about_feature_1_text',
        'about_feature_2_title', 'about_feature_2_text',
        'about_feature_3_title', 'about_feature_3_text',
        'about_cta_title', 'about_cta_text',
        'contact_kicker', 'contact_title', 'contact_text', 'contact_hours',
        'rooms_page_kicker', 'rooms_page_title',
        'gallery_kicker', 'gallery_title',
        'footer_plan',
    ];

    public const HOME_LONG_KEYS = [
        'hero_text', 'faq_intro', 'about_text', 'contact_text', 'footer_plan',
        'about_feature_1_text', 'about_feature_2_text', 'about_feature_3_text', 'about_cta_text',
    ];

    private static ?self $current = null;

    /**
     * The single settings row, loaded once per request. Never returns null,
     * so a fresh install without settings doesn't crash every page.
     */
    public static function current(): self
    {
        if (static::$current === null) {
            try {
                static::$current = static::query()->orderBy('id')->first() ?? new static();
            } catch (\Throwable $e) {
                static::$current = new static();
            }
        }

        return static::$current;
    }

    public static function flushCurrent(): void
    {
        static::$current = null;
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCurrent());
    }

    /** Colours the hotel can change from Admin > Setting > Brand colours. Must match public/hms-tokens.css. */
    public const THEME_DEFAULTS = [
        'forest' => '#14241b',
        'canopy' => '#1f3a2b',
        'leaf'   => '#3f6b4a',
        'sand'   => '#c9b88a',
        'paper'  => '#f4f3ec',
        'card'   => '#fbfbf6',
        'ink'    => '#15201a',
        'muted'  => '#5d695f',
    ];

    /** Saved colours merged over the defaults. Invalid values are ignored. */
    public function themeColors(): array
    {
        $saved = is_array($this->theme ?? null) ? $this->theme : [];
        $out = self::THEME_DEFAULTS;
        foreach ($saved as $key => $value) {
            if (array_key_exists($key, $out) && is_string($value) && preg_match('/^#[0-9a-f]{6}$/i', $value)) {
                $out[$key] = strtolower($value);
            }
        }

        return $out;
    }

    public function themeIsCustom(): bool
    {
        return $this->themeColors() !== self::THEME_DEFAULTS;
    }

    /** CSS variables that override /hms-tokens.css, or '' when nothing was changed. */
    public function themeCss(): string
    {
        $c = $this->themeColors();
        $d = self::THEME_DEFAULTS;
        $changed = fn (string ...$keys) => count(array_filter($keys, fn ($k) => $c[$k] !== $d[$k])) > 0;
        $rgb = fn (string $hex) => implode(', ', sscanf($hex, '#%02x%02x%02x'));
        $mix = function (string $a, string $b, float $t) {
            [$r1, $g1, $b1] = sscanf($a, '#%02x%02x%02x');
            [$r2, $g2, $b2] = sscanf($b, '#%02x%02x%02x');

            return sprintf('#%02x%02x%02x', round($r1 + ($r2 - $r1) * $t), round($g1 + ($g2 - $g1) * $t), round($b1 + ($b2 - $b1) * $t));
        };

        $v = [];
        if ($changed('forest')) {
            $v['--hms-forest'] = $c['forest'];
            $v['--hms-forest-rgb'] = $rgb($c['forest']);
            $v['--hms-forest-2'] = $mix($c['forest'], '#ffffff', 0.06);
        }
        if ($changed('canopy')) {
            $v['--hms-canopy'] = $c['canopy'];
            $v['--hms-canopy-rgb'] = $rgb($c['canopy']);
            $v['--hms-brand-dark'] = $c['canopy'];
        }
        if ($changed('leaf')) {
            $v['--hms-leaf'] = $c['leaf'];
            $v['--hms-leaf-rgb'] = $rgb($c['leaf']);
        }
        if ($changed('canopy', 'leaf')) {
            $v['--hms-brand'] = $mix($c['canopy'], $c['leaf'], 0.5);
        }
        if ($changed('sand')) {
            $v['--hms-sand'] = $c['sand'];
            $v['--hms-sand-rgb'] = $rgb($c['sand']);
        }
        if ($changed('paper')) {
            $v['--hms-paper'] = $c['paper'];
            $v['--hms-paper-rgb'] = $rgb($c['paper']);
            $v['--hms-on-dark'] = $mix($c['paper'], '#ffffff', 0.0);
        }
        if ($changed('card')) {
            $v['--hms-card'] = $c['card'];
        }
        if ($changed('ink')) {
            $v['--hms-ink'] = $c['ink'];
            $v['--hms-nav'] = $c['ink'];
        }
        if ($changed('canopy')) {
            $v['--hms-nav-hover'] = $c['canopy'];
        }
        if ($changed('muted')) {
            $v['--hms-muted'] = $c['muted'];
        }
        if ($changed('paper', 'ink')) {
            $v['--hms-soft'] = $mix($c['paper'], $c['ink'], 0.05);
            $v['--hms-soft-2'] = $mix($c['paper'], $c['ink'], 0.10);
            $v['--hms-line'] = $mix($c['paper'], $c['ink'], 0.13);
            $v['--hms-line-strong'] = $mix($c['paper'], $c['ink'], 0.22);
        }

        if (! $v) {
            return '';
        }

        $css = '';
        foreach ($v as $name => $value) {
            $css .= $name . ':' . $value . ';';
        }

        // Keep nav readable in dark mode even when light-theme tokens were customized.
        $dark = '--hms-nav:#eef0e6;--hms-nav-hover:#c9b88a;--hms-nav-on-dark:#eef0e6;';

        return ':root{' . $css . '}html.theme-dark{' . $dark . '}';
    }

    /** Hotel name shown across the site. */
    public function getNameAttribute(): string
    {
        return $this->site_name ?: config('app.name', 'Hotel');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset($this->logo) : null;
    }

    /** White version of the logo for dark backgrounds: "<logo>-light.<ext>" when it exists. */
    public function getLogoLightUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }
        $light = preg_replace('/(\.[a-z0-9]+)$/i', '-light$1', $this->logo);

        return is_file(public_path($light)) ? asset($light) : null;
    }

    public function getCopyrightTextAttribute(): string
    {
        return $this->copyright ?: '© ' . date('Y') . ' ' . $this->name . '. All rights reserved.';
    }

    /** About heading. A custom line wins; otherwise the tagline from Site Setting. */
    public function aboutTitle(): string
    {
        $saved = data_get($this->homepage, 'about_title');
        if (filled($saved) && $saved !== self::HOME_DEFAULTS['about_title']) {
            return (string) $saved;
        }

        return $this->tagline ?: $this->home('about_title');
    }

    /** About paragraph. A custom line wins; otherwise the site description. */
    public function aboutText(): string
    {
        $saved = data_get($this->homepage, 'about_text');
        if (filled($saved) && $saved !== self::HOME_DEFAULTS['about_text']) {
            return (string) $saved;
        }

        return $this->site_description ?: $this->home('about_text');
    }

    /** One homepage line. A blank value falls back to the sample copy. */
    public function home(string $key): string
    {
        $value = data_get($this->homepage, $key);

        return filled($value) ? (string) $value : (string) (self::HOME_DEFAULTS[$key] ?? '');
    }

    public function heroImageUrl(): string
    {
        $path = data_get($this->homepage, 'hero_image');
        if ($path && is_file(public_path($path))) {
            return asset($path);
        }

        return asset('frontend/assets/img/hotel/facade.jpg');
    }

    public function faqImageUrl(): string
    {
        $path = data_get($this->homepage, 'faq_image');
        if ($path && is_file(public_path($path))) {
            return asset($path);
        }

        return asset('frontend/assets/img/hotel/lounge.jpg');
    }

    /** USD is shown as US$. Other codes keep their own symbol or the code itself. */
    public function formatMoney(float|int|string|null $amount, int $decimals = 0): string
    {
        $formatted = number_format((float) $amount, $decimals);
        $code = strtoupper(trim((string) ($this->default_currency ?: 'USD')));

        return match ($code) {
            'USD' => 'US$' . $formatted,
            'EUR' => '€' . $formatted,
            'GBP' => '£' . $formatted,
            'ZAR' => 'R' . $formatted,
            default => $code . ' ' . $formatted,
        };
    }
}
