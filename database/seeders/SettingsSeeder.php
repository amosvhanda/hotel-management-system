<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\SmtpSetting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        if (! SiteSetting::exists()) {
            SiteSetting::create([
                'site_name'        => config('app.name', 'Hotel'),
                'tagline'          => 'Comfortable rooms and warm hospitality',
                'site_description' => 'Comfortable, well-kept rooms, friendly staff and simple online booking.',
                'phone'            => '+263 77 000 0000',
                'email'            => 'reservations@' . config('app.domain'),
                'address'          => 'Harare, Zimbabwe',
                'copyright'        => '© ' . date('Y') . ' ' . config('app.name', 'Hotel') . '. All rights reserved.',
            ]);
        }

        // Leave host empty so .env mail settings are used until SMTP is filled in from the admin panel.
        if (! SmtpSetting::exists()) {
            SmtpSetting::create(['mailer' => 'smtp', 'port' => '587', 'encryption' => 'tls']);
        }
    }
}
