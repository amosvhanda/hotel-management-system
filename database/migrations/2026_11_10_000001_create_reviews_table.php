<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Guest "rate your stay" reviews: one per booking, approved by staff before they show. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reviews')) {
            return;
        }

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('rooms_id')->index();
            $table->unsignedTinyInteger('rating');            // 1–5 stars
            $table->string('title', 120)->nullable();
            $table->text('comment');
            $table->unsignedTinyInteger('status')->default(0); // 0 pending, 1 approved, 2 hidden
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
