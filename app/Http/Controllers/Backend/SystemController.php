<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/** Messages to guests: what was sent, what failed, and retrying failures. */
class SystemController extends Controller
{
    public function messages()
    {
        $failedJobs = DB::table('failed_jobs')->latest('failed_at')->limit(50)->get()->map(function ($j) {
            $payload = json_decode($j->payload, true) ?: [];
            $j->name = class_basename($payload['displayName'] ?? 'Job');
            $j->error = \Illuminate\Support\Str::limit(strtok((string) $j->exception, "\n"), 200);

            return $j;
        });
        $recent = MessageLog::with('booking')->latest('id')->limit(100)->get();
        $waiting = DB::table('jobs')->count();

        return view('backend.system.messages', compact('failedJobs', 'recent', 'waiting'));
    }

    public function retry(string $uuid)
    {
        Artisan::call('queue:retry', ['id' => [$uuid]]);

        return back()->with(['message' => 'Message queued to be sent again.', 'alert-type' => 'success']);
    }

    public function forget(string $uuid)
    {
        Artisan::call('queue:forget', ['id' => $uuid]);

        return back()->with(['message' => 'Failed message removed.', 'alert-type' => 'success']);
    }
}
