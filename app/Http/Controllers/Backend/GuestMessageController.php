<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\GuestMessageMail;
use App\Models\Booking;
use App\Models\SiteSetting;
use App\Providers\AppServiceProvider;
use App\Support\GuestMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/** Admin → Settings → Guest messages: switch on/off, edit wording, WhatsApp number, send a test. */
class GuestMessageController extends Controller
{
    public function edit()
    {
        $site = SiteSetting::current();
        $templates = collect(GuestMessages::TEMPLATES)->map(fn ($d, $key) => GuestMessages::get($key) + ['key' => $key]);

        return view('backend.setting.guest_messages', compact('site', 'templates'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'whatsapp_number' => 'nullable|string|max:30',
            'messages' => 'array',
            'messages.*.subject' => 'nullable|string|max:200',
            'messages.*.body' => 'nullable|string|max:5000',
        ]);

        $saved = [];
        foreach (array_keys(GuestMessages::TEMPLATES) as $key) {
            $in = $request->input("messages.$key", []);
            $default = GuestMessages::TEMPLATES[$key];
            $saved[$key] = [
                'enabled' => ! empty($in['enabled']),
                // Store only real changes, so improved default wording still reaches untouched messages.
                'subject' => trim((string) ($in['subject'] ?? '')) === $default['subject'] ? null : ($in['subject'] ?? null),
                'body'    => str_replace("\r\n", "\n", trim((string) ($in['body'] ?? ''))) === $default['body'] ? null : ($in['body'] ?? null),
            ];
        }

        $site = SiteSetting::orderBy('id')->firstOrFail();
        $site->update(['messages' => $saved, 'whatsapp_number' => $request->whatsapp_number]);
        SiteSetting::flushCurrent();

        return back()->with(['message' => 'Guest messages saved.', 'alert-type' => 'success']);
    }

    /** Sends the message, filled with the latest booking (or sample data), to the signed-in staff member right now. */
    public function test(string $key)
    {
        abort_unless(array_key_exists($key, GuestMessages::TEMPLATES), 404);

        $booking = Booking::with('room.type')->latest('id')->first();
        if (! $booking) {
            return back()->with(['message' => 'Make a booking first — the test uses your latest booking\'s details.', 'alert-type' => 'error']);
        }

        try {
            AppServiceProvider::applySmtpSettings();
            Mail::purge();
            $t = GuestMessages::get($key);
            $v = GuestMessages::values($booking);
            Mail::to(Auth::user()->email)->send(new GuestMessageMail(
                '[TEST] ' . GuestMessages::fill($t['subject'], $v),
                GuestMessages::fill($t['body'], $v),
                $t['button'] ?? null,
                GuestMessages::link($key, $booking),
            ));
        } catch (\Throwable $e) {
            return back()->with(['message' => 'Test email failed: ' . $e->getMessage() . ' — check SMTP settings.', 'alert-type' => 'error']);
        }

        return back()->with(['message' => 'Test sent to ' . Auth::user()->email . '.', 'alert-type' => 'success']);
    }
}
