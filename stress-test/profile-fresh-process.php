<?php

declare(strict_types=1);

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

$app = require __DIR__ . '/../bootstrap/app.php';
$req = Request::create('/api/campaigns', 'GET', ['page' => '1', 'per_page' => '15']);
$req->headers->set('Accept', 'application/json');
$req->server->set('REMOTE_ADDR', '127.0.0.1');
$app->instance('request', $req);
$app->instance(Request::class, $req);

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$queries = [];
DB::connection()->listen(static function ($q) use (&$queries): void {
    $queries[] = ['sql' => $q->sql, 'ms' => (float) $q->time];
});

Cache::tags(['campaigns'])->flush();
Cache::forget('campaigns:count:v4:all');

$t = microtime(true);
$response = $kernel->handle($req);
$total = round((microtime(true) - $t) * 1000, 3);

echo "CACHE MISS — fresh PHP process\n";
echo "HTTP handle total: {$total} ms\n";
echo 'Query count: ' . count($queries) . "\n";
echo 'Response bytes: ' . strlen($response->getContent() ?: '') . "\n\n";

foreach ($queries as $i => $q) {
    echo 'Q' . ($i + 1) . ": {$q['ms']} ms\n";
    echo $q['sql'] . "\n\n";
}

// Warm hit in same process
$queries = [];
$t2 = microtime(true);
$kernel->handle($req);
$hitTotal = round((microtime(true) - $t2) * 1000, 3);

echo "CACHE HIT — same PHP process (warm)\n";
echo "HTTP handle total: {$hitTotal} ms\n";
echo 'Query count: ' . count($queries) . "\n";
