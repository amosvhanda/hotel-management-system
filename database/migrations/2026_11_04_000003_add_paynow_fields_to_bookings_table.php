<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'paynow_poll_url')) {
                $table->string('paynow_poll_url')->nullable()->after('transation_id');
            }
            if (! Schema::hasColumn('bookings', 'paynow_status')) {
                $table->string('paynow_status')->nullable()->after('paynow_poll_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['paynow_poll_url', 'paynow_status']);
        });
    }
};
