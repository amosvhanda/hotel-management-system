<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('site_settings', 'homepage')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->json('homepage')->nullable();
            });
        }

        if (! Schema::hasColumn('galleries', 'caption')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->string('caption')->nullable()->after('photo_name');
            });
        }

        if (! Schema::hasColumn('galleries', 'show_on_home')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->boolean('show_on_home')->default(false);
            });
        }

        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');

        Schema::table('galleries', function (Blueprint $table) {
            if (Schema::hasColumn('galleries', 'show_on_home')) {
                $table->dropColumn('show_on_home');
            }
            if (Schema::hasColumn('galleries', 'caption')) {
                $table->dropColumn('caption');
            }
        });

        if (Schema::hasColumn('site_settings', 'homepage')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropColumn('homepage');
            });
        }
    }
};
