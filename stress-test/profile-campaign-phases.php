<?php

/**
 * Detailed in-request phase profiling via Laravel events.
 * Run AFTER cache warm: docker compose exec app php stress-test/profile-campaign-phases.php
 */

declare(strict_types=1);

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Routing\Events\RouteMatched;

function ms(float $start): float
{
    return round((microtime(true) - $start) * 1000, 3);
}

function makeRequest(): Request
{
    $request = Request::create('/api/campaigns', 'GET', ['page' => '1', 'per_page' => '15']);
    $request->headers->set('Accept', 'application/json');
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    return $request;
}

$app = require __DIR__ . '/../bootstrap/app.php';
$request = makeRequest();
$app->instance('request', $request);
$app->instance(Request::class, $request);
/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$phases = [
    'handle_start' => microtime(true),
    'route_matched' => null,
    'first_query' => null,
    'last_query' => null,
    'handle_end' => null,
];
$queries = [];

Event::listen(RouteMatched::class, static function () use (&$phases): void {
    if ($phases['route_matched'] === null) {
        $phases['route_matched'] = microtime(true);
    }
});

DB::connection()->listen(static function ($query) use (&$queries, &$phases): void {
    $now = microtime(true);
    if ($phases['first_query'] === null) {
        $phases['first_query'] = $now;
    }
    $phases['last_query'] = $now;
    $queries[] = [
        'sql' => $query->sql,
        'time_ms' => (float) $query->time,
    ];
});

// Ensure cache HIT
$cacheKey = 'campaigns:all:v4:per_page:15:page:1';
try {
    if (Cache::tags(['campaigns'])->get($cacheKey) === null) {
        $kernel->handle(makeRequest());
    }
} catch (\BadMethodCallException) {
    if (Cache::get($cacheKey) === null) {
        $kernel->handle(makeRequest());
    }
}

echo "=== IN-REQUEST PHASE TIMING (cache HIT, single run) ===\n\n";

// Rate limiter isolated (same request IP)
$t = microtime(true);
RateLimiter::attempt(sha1('127.0.0.1') . '|api/campaigns', 1000, static fn (): bool => true, 60);
$rateMs = ms($t);

$t = microtime(true);
try {
    Cache::tags(['campaigns'])->get($cacheKey);
} catch (\BadMethodCallException) {
    Cache::get($cacheKey);
}
$cacheMs = ms($t);

$phases = [
    'handle_start' => microtime(true),
    'route_matched' => null,
    'first_query' => null,
    'last_query' => null,
    'handle_end' => null,
];
$queries = [];

Event::listen(RouteMatched::class, static function () use (&$phases): void {
    if ($phases['route_matched'] === null) {
        $phases['route_matched'] = microtime(true);
    }
});

DB::connection()->listen(static function ($query) use (&$queries, &$phases): void {
    $now = microtime(true);
    if ($phases['first_query'] === null) {
        $phases['first_query'] = $now;
    }
    $phases['last_query'] = $now;
    $queries[] = ['sql' => $query->sql, 'time_ms' => (float) $query->time];
});

$req = makeRequest();
$app->instance('request', $req);
$app->instance(Request::class, $req);

$response = $kernel->handle($req);
$phases['handle_end'] = microtime(true);

$tJson = microtime(true);
$body = $response->getContent();
json_encode(json_decode($body, true, 512, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR);
$jsonMs = ms($tJson);

$totalMs = ($phases['handle_end'] - $phases['handle_start']) * 1000;
$preRouteMs = $phases['route_matched']
    ? ($phases['route_matched'] - $phases['handle_start']) * 1000
    : 0.0;
$postRouteMs = $phases['route_matched']
    ? ($phases['handle_end'] - $phases['route_matched']) * 1000
    : $totalMs;
$queryMs = array_sum(array_column($queries, 'time_ms'));

printf("  Rate limiter (isolated):     %8.3f ms\n", $rateMs);
printf("  Cache lookup (isolated):     %8.3f ms\n", $cacheMs);
printf("  Pre-route (middleware boot): %8.3f ms\n", $preRouteMs);
printf("  Post-route (controller+view):%8.3f ms\n", $postRouteMs);
printf("  MySQL (during request):      %8.3f ms  (%d queries)\n", $queryMs, count($queries));
printf("  JSON re-serialize body:      %8.3f ms\n", $jsonMs);
printf("  HTTP handle total:           %8.3f ms\n", $totalMs);
printf("  Response bytes:              %d\n", strlen($body ?: ''));
printf("  HTTP status:                 %d\n", $response->getStatusCode());

$kernel->terminate($req, $response);

// CACHE MISS run
echo "\n=== IN-REQUEST PHASE TIMING (cache MISS, single run) ===\n\n";

Cache::tags(['campaigns'])->flush();
Cache::forget('campaigns:count:v4:all');

$phases = [
    'handle_start' => microtime(true),
    'route_matched' => null,
    'first_query' => null,
    'last_query' => null,
    'handle_end' => null,
];
$queries = [];

Event::listen(RouteMatched::class, static function () use (&$phases): void {
    if ($phases['route_matched'] === null) {
        $phases['route_matched'] = microtime(true);
    }
});

DB::connection()->listen(static function ($query) use (&$queries, &$phases): void {
    $now = microtime(true);
    if ($phases['first_query'] === null) {
        $phases['first_query'] = $now;
    }
    $phases['last_query'] = $now;
    $queries[] = ['sql' => $query->sql, 'time_ms' => (float) $query->time];
});

$req2 = makeRequest();
$app->instance('request', $req2);
$app->instance(Request::class, $req2);

$response2 = $kernel->handle($req2);
$phases['handle_end'] = microtime(true);

$tJson2 = microtime(true);
$body2 = $response2->getContent();
json_encode(json_decode($body2, true, 512, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR);
$jsonMs2 = ms($tJson2);

$totalMs2 = ($phases['handle_end'] - $phases['handle_start']) * 1000;
$preRouteMs2 = $phases['route_matched']
    ? ($phases['route_matched'] - $phases['handle_start']) * 1000
    : 0.0;
$postRouteMs2 = $phases['route_matched']
    ? ($phases['handle_end'] - $phases['route_matched']) * 1000
    : $totalMs2;
$queryMs2 = array_sum(array_column($queries, 'time_ms'));

printf("  Pre-route (middleware boot): %8.3f ms\n", $preRouteMs2);
printf("  Post-route (controller+repo):%8.3f ms\n", $postRouteMs2);
printf("  MySQL (during request):      %8.3f ms  (%d queries)\n", $queryMs2, count($queries));
printf("  JSON re-serialize body:      %8.3f ms\n", $jsonMs2);
printf("  HTTP handle total:           %8.3f ms\n", $totalMs2);
printf("  Response bytes:              %d\n", strlen($body2 ?: ''));

foreach ($queries as $i => $q) {
    echo "\n  Query #" . ($i + 1) . " — {$q['time_ms']} ms\n";
    echo '  ' . $q['sql'] . "\n";
}

echo "\nDone.\n";
