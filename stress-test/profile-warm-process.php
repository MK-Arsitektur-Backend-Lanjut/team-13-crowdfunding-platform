<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

function ms(float $start): float
{
    return round((microtime(true) - $start) * 1000, 3);
}

$app = require __DIR__ . '/../bootstrap/app.php';
$req = Request::create('/api/campaigns', 'GET', ['page' => '1', 'per_page' => '15']);
$req->headers->set('Accept', 'application/json');
$req->server->set('REMOTE_ADDR', '127.0.0.1');
$app->instance('request', $req);
$app->instance(Request::class, $req);

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// Warm everything
$kernel->handle($req);

echo "=== WARM PROCESS — component medians (5 runs each) ===\n\n";

$rate = [];
$cacheHit = [];
$httpHit = [];
$httpMiss = [];

for ($i = 0; $i < 5; $i++) {
    $t = microtime(true);
    RateLimiter::attempt(sha1('127.0.0.1') . '|api/campaigns', 1000, static fn (): bool => true, 60);
    $rate[] = ms($t);

    $t = microtime(true);
    Cache::tags(['campaigns'])->get('campaigns:all:v4:per_page:15:page:1');
    $cacheHit[] = ms($t);

    $queries = [];
    DB::connection()->listen(static function ($q) use (&$queries): void {
        $queries[] = (float) $q->time;
    });
    $t = microtime(true);
    $kernel->handle($req);
    $httpHit[] = ms($t);

    Cache::tags(['campaigns'])->flush();
    Cache::forget('campaigns:count:v4:all');
    $queries = [];
    DB::connection()->listen(static function ($q) use (&$queries): void {
        $queries[] = (float) $q->time;
    });
    $t = microtime(true);
    $kernel->handle($req);
    $elapsed = ms($t);
    $httpMiss[] = ['total' => $elapsed, 'mysql' => array_sum($queries), 'count' => count($queries)];
}

function med(array $v): float
{
    sort($v);
    $n = count($v);
    $m = intdiv($n, 2);

    return $n % 2 === 0 ? ($v[$m - 1] + $v[$m]) / 2 : $v[$m];
}

printf("Rate limiter Redis:     %8.3f ms\n", med($rate));
printf("Cache lookup Redis:     %8.3f ms\n", med($cacheHit));
printf("HTTP cache HIT:         %8.3f ms\n", med($httpHit));
$missTotals = array_column($httpMiss, 'total');
$missMysql = array_column($httpMiss, 'mysql');
printf("HTTP cache MISS total:  %8.3f ms\n", med($missTotals));
printf("HTTP cache MISS MySQL:  %8.3f ms  (%d queries median)\n", med($missMysql), (int) med(array_map('floatval', array_column($httpMiss, 'count'))));

echo "\nPer-run MISS detail:\n";
foreach ($httpMiss as $i => $m) {
    echo '  run ' . ($i + 1) . ": total={$m['total']} ms, mysql={$m['mysql']} ms, queries={$m['count']}\n";
}
