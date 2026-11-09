<?php
/**
 * Post-deploy smoke test, run INSIDE the app container as www-data (the same user
 * as PHP-FPM). Renders real pages through Laravel and, if any returns 5xx, prints
 * the exact exception so the GitHub Actions log shows the cause.
 *
 *   su -s /bin/sh www-data -c "php deploy/smoke.php"
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$base = rtrim((string) (getenv('APP_URL') ?: 'https://localhost'), '/');
$paths = ['/', '/rooms', '/about', '/contact', '/login', '/register', '/admin/login'];
$failed = 0;

foreach ($paths as $path) {
    $request = Illuminate\Http\Request::create($base . $path, 'GET', [], [], [], ['HTTPS' => 'on']);
    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        $e = $response->exception ?? null;
    } catch (Throwable $t) {
        $status = 500;
        $e = $t;
    }
    echo str_pad($path, 14) . " → {$status}\n";

    if ($status >= 500) {
        $failed++;
        if ($e) {
            echo "   ERROR: " . get_class($e) . ': ' . $e->getMessage() . "\n";
            echo "   at " . $e->getFile() . ':' . $e->getLine() . "\n";
            foreach (array_slice(explode("\n", $e->getTraceAsString()), 0, 6) as $line) {
                echo "     {$line}\n";
            }
        }
    }
    $kernel->terminate($request, $response ?? new Illuminate\Http\Response('', $status));
    // Fresh state for the next page (e.g. the cached SiteSetting).
    if (class_exists(App\Models\SiteSetting::class)) {
        App\Models\SiteSetting::flushCurrent();
    }
}

if ($failed) {
    echo "SMOKE TEST FAILED: {$failed} page(s) returned 5xx\n";
    exit(1);
}
echo "Smoke test OK — all pages render.\n";
