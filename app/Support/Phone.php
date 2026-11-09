<?php

namespace App\Support;

/** Zimbabwe-friendly phone helpers for WhatsApp links. */
class Phone
{
    /** 0771 234 567 / +263 77 123 4567 / 263771234567 → 263771234567. Null if it doesn't look like a number. */
    public static function international(?string $phone, string $country = '263'): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if (strlen($digits) < 9) {
            return null;
        }
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '0')) {
            $digits = $country . substr($digits, 1);
        } elseif (strlen($digits) === 9) {
            $digits = $country . $digits;
        }

        return $digits;
    }

    public static function whatsappLink(?string $phone, string $text = ''): ?string
    {
        $n = static::international($phone);

        return $n ? 'https://wa.me/' . $n . ($text !== '' ? '?text=' . rawurlencode($text) : '') : null;
    }
}
