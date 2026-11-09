<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $appUrl = (string) config('app.url');

        // Behind Traefik, PHP sees HTTP on the internal network. Always generate
        // absolute links/assets from APP_URL so HTTPS pages never emit http:// assets.
        if (str_starts_with($appUrl, 'https://')) {
            URL::forceRootUrl(rtrim($appUrl, '/'));
            URL::forceScheme('https');
        }

        static::applySmtpSettings();
    }

    /**
     * Use the SMTP details saved in Admin → Settings → SMTP, when they are filled in.
     * Only the smtp mailer and the "from" address are overridden, so .env keeps working otherwise.
     */
    /** Public so the queue worker can re-read SMTP settings before each email (staff may change them any time). */
    public static function applySmtpSettings(): void
    {
        try {
            if (! Schema::hasTable('smtp_settings')) {
                return;
            }

            $smtp = SmtpSetting::orderBy('id')->first();
        } catch (\Throwable $e) {
            return; // database not ready (e.g. during install)
        }

        if (! $smtp || blank($smtp->host)) {
            return;
        }

        Config::set('mail.default', $smtp->mailer ?: 'smtp');
        Config::set('mail.mailers.smtp', array_merge(config('mail.mailers.smtp', []), [
            'transport'  => 'smtp',
            'host'       => $smtp->host,
            'port'       => (int) ($smtp->port ?: 587),
            'username'   => $smtp->username,
            'password'   => $smtp->password,
            'encryption' => $smtp->encryption ?: null,
        ]));

        Config::set('mail.from', [
            'address' => $smtp->from_address ?: config('mail.from.address'),
            'name'    => rescue(fn () => SiteSetting::current()->name, config('app.name'), false),
        ]);
    }
}
