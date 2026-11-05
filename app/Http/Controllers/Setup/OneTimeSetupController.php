<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

/**
 * One-time setup page: brings the database up to date without the command line.
 * After one successful run it locks itself (storage/app/setup.lock) and returns 404.
 */
class OneTimeSetupController extends Controller
{
    public function __invoke()
    {
        $lock = storage_path('app/setup.lock');
        abort_if(is_file($lock), 404);

        $steps = [];
        $ok = true;

        foreach ([['migrate', ['--force' => true]], ['optimize:clear', []]] as [$cmd, $args]) {
            try {
                $code = Artisan::call($cmd, $args);
                $steps[] = [$cmd, $code === 0, trim(Artisan::output())];
                $ok = $ok && $code === 0;
            } catch (\Throwable $e) {
                $steps[] = [$cmd, false, $e->getMessage()];
                $ok = false;
            }
        }

        if ($ok) {
            @file_put_contents($lock, now()->toDateTimeString());
        }

        return response()->view('setup.result', compact('ok', 'steps'));
    }
}
