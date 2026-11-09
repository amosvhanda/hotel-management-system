<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Every guest message (email / WhatsApp): what, to whom, when, and whether it worked. Stops duplicates. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('message_logs')) {
            return;
        }
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->string('channel', 20);          // email | whatsapp
            $table->string('template', 60);         // booking_received, pre_arrival, ...
            $table->string('recipient');
            $table->string('status', 20)->default('queued'); // queued | sent | failed | skipped
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->unique(['booking_id', 'channel', 'template'], 'message_once_per_booking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_logs');
    }
};
