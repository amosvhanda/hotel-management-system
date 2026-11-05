<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('favicon')->nullable()->after('logo');
            $table->string('support_email')->nullable()->after('email');
            $table->string('instagram')->nullable()->after('twitter');
            $table->string('linkedin')->nullable()->after('instagram');
            $table->string('youtube')->nullable()->after('linkedin');
            $table->string('site_name')->nullable()->after('youtube');
            $table->string('tagline')->nullable()->after('site_name');
            $table->longText('site_description')->nullable()->after('tagline');
            $table->string('meta_title')->nullable()->after('site_description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            $table->string('google_analytics')->nullable()->after('meta_keywords');
            $table->string('timezone')->nullable()->after('google_analytics');
            $table->string('language')->nullable()->after('timezone');
            $table->boolean('maintenance_mode')->default(false)->after('language');
            $table->text('custom_css')->nullable()->after('maintenance_mode');
            $table->text('custom_js')->nullable()->after('custom_css');
            $table->boolean('social_sharing_enabled')->default(true)->after('custom_js');
            $table->string('default_currency')->nullable()->after('social_sharing_enabled');
            $table->string('default_locale')->nullable()->after('default_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'favicon',
                'support_email',
                'instagram',
                'linkedin',
                'youtube',
                'site_name',
                'tagline',
                'site_description',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'google_analytics',
                'timezone',
                'language',
                'maintenance_mode',
                'custom_css',
                'custom_js',
                'social_sharing_enabled',
                'default_currency',
                'default_locale',
            ]);
        });
    }
};
