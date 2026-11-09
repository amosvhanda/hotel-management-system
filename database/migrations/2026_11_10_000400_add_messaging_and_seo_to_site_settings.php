<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Guest message templates (on/off + wording), WhatsApp number, Google Search Console code. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'messages')) {
                $table->json('messages')->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'whatsapp_number')) {
                $table->string('whatsapp_number', 30)->nullable();
            }
            if (! Schema::hasColumn('site_settings', 'google_site_verification')) {
                $table->string('google_site_verification', 120)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            foreach (['messages', 'whatsapp_number', 'google_site_verification'] as $col) {
                if (Schema::hasColumn('site_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
