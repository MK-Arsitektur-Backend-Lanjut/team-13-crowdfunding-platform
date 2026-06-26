<?php

/**
 * Profiling script for GET /api/campaigns?page=1&per_page=15
 * Run: docker compose exec app php stress-test/profile-campaign-index.php
 */

declare(strict_types=1);

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

use App\Models\Campaign;
use App\Repositories\CampaignRepository;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

function ms(float $start): float
{
    return round((microtime(true) - $start) * 1000, 3);
}

function section(string $title): void
{
    echo "\n" . str_repeat('=', 72) . "\n";
    echo $title . "\n";
    echo str_repeat('=', 72) . "\n";
}

function line(string $label, float $valueMs, ?string $note = null): void
{
    $suffix = $note !== null ? "  ({$note})" : '';
    printf("  %-36s %8.3f ms%s\n", $label . ':', $valueMs, $suffix);
}

function median(array $values): float
{
    sort($values);
    $n = count($values);
    $mid = intdiv($n, 2);

    return $n % 2 === 0 ? ($values[$mid - 1] + $values[$mid]) / 2 : $values[$mid];
}

function makeRequest(): Request
{
    $request = Request::create('/api/campaigns', 'GET', [
        'page' => '1',
        'per_page' => '15',
    ]);
    $request->headers->set('Accept', 'application/json');
    $request->server->set('REMOTE_ADDR', '127.0.0.1');

    return $request;
}

function bindRequest($app, Request $request): void
{
    $app->instance('request', $request);
    $app->instance(Request::class, $request);
}

function runHttp($app, Request $request, array &$queries): array
{
    bindRequest($app, $request);

    /** @var Kernel $kernel */
    $kernel = $app->make(Kernel::class);

    $before = count($queries);
    $start = microtime(true);
    /** @var Response $response */
    $response = $kernel->handle($request);
    $elapsed = ms($start);
    $newQueries = array_slice($queries, $before);
    $kernel->terminate($request, $response);

    return [
        'ms' => $elapsed,
        'response' => $response,
        'queries' => $newQueries,
        'query_ms' => array_sum(array_column($newQueries, 'time_ms')),
    ];
}

// ---------------------------------------------------------------------------
// Bootstrap
// ---------------------------------------------------------------------------
section('PHASE 1 — Laravel bootstrap');

$bootstrapStart = microtime(true);
$app = require __DIR__ . '/../bootstrap/app.php';
bindRequest($app, makeRequest());
/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
$bootstrapMs = ms($bootstrapStart);
line('Bootstrap (app + kernel bootstrap)', $bootstrapMs);

$queries = [];
DB::connection()->listen(static function ($query) use (&$queries): void {
    $queries[] = [
        'sql' => $query->sql,
        'bindings' => $query->bindings,
        'time_ms' => (float) $query->time,
    ];
});

$cacheKey = 'campaigns:all:v4:per_page:15:page:1';
$throttleKey = sha1('127.0.0.1') . '|api/campaigns';

// Warm cache via HTTP
section('WARMUP — populate Redis cache');
$warm = runHttp($app, makeRequest(), $queries);
line('Warmup HTTP request', $warm['ms'], 'status ' . $warm['response']->getStatusCode());

// ---------------------------------------------------------------------------
// Isolated micro-benchmarks
// ---------------------------------------------------------------------------
section('ISOLATED — Rate limiter Redis (5 samples)');

$rateSamples = [];
for ($i = 0; $i < 5; $i++) {
    $t = microtime(true);
    RateLimiter::attempt($throttleKey, 1000, static fn (): bool => true, 60);
    $rateSamples[] = ms($t);
}
line('RateLimiter::attempt median', median($rateSamples));

section('ISOLATED — Cache lookup Redis (5 samples, cache HIT)');

$cacheSamples = [];
for ($i = 0; $i < 5; $i++) {
    $t = microtime(true);
    try {
        Cache::tags(['campaigns'])->get($cacheKey);
    } catch (\BadMethodCallException) {
        Cache::get($cacheKey);
    }
    $cacheSamples[] = ms($t);
}
line('Cache::tags get median', median($cacheSamples));

section('ISOLATED — MySQL + hydration + JSON (CACHE MISS)');

Cache::tags(['campaigns'])->flush();
Cache::forget('campaigns:count:v4:all');

$missQueries = [];
DB::connection()->listen(static function ($query) use (&$missQueries): void {
    $missQueries[] = [
        'sql' => $query->sql,
        'bindings' => $query->bindings,
        'time_ms' => (float) $query->time,
    ];
});

$tRepo = microtime(true);
$repo = $app->make(CampaignRepository::class);
$paginator = $repo->getAll(15);
$repoTotalMs = ms($tRepo);
$mysqlMs = array_sum(array_column($missQueries, 'time_ms'));

$tHydrate = microtime(true);
$items = collect($paginator->items())
    ->map(static fn ($model) => $model instanceof Campaign ? $model->toArray() : (array) $model)
    ->all();
$hydrationMs = ms($tHydrate);

$tJson = microtime(true);
$jsonString = json_encode($paginator, JSON_THROW_ON_ERROR);
$jsonMs = ms($tJson);

line('Repository getAll() wall clock', $repoTotalMs);
line('MySQL (DB::listen sum)', $mysqlMs, count($missQueries) . ' queries');
line('Model toArray() hydration', $hydrationMs, count($items) . ' rows');
line('JSON encode paginator', $jsonMs, strlen($jsonString) . ' bytes');

// Re-warm cache
$repo->getAll(15);

// ---------------------------------------------------------------------------
// Full HTTP — cache HIT (5 runs)
// ---------------------------------------------------------------------------
section('FULL HTTP — cache HIT (5 runs, median)');

$httpSamples = [];
$querySamples = [];
$jsonSamples = [];
$lastResponse = null;

for ($i = 0; $i < 5; $i++) {
    $result = runHttp($app, makeRequest(), $queries);
    $httpSamples[] = $result['ms'];
    $querySamples[] = $result['query_ms'];
    $lastResponse = $result['response'];

    $tJson = microtime(true);
    json_encode(json_decode($result['response']->getContent(), true, 512, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR);
    $jsonSamples[] = ms($tJson);
}

$httpMedian = median($httpSamples);
$queryMedian = median($querySamples);
$rateMedian = median($rateSamples);
$cacheMedian = median($cacheSamples);
$jsonMedian = median($jsonSamples);

// Middleware estimate = HTTP - known components that run inside controller path
// On cache HIT: controller ≈ cache get + paginator json response build
$middlewareMs = max(0.0, round($httpMedian - $queryMedian - $cacheMedian - $jsonMedian, 3));

// ---------------------------------------------------------------------------
// Full HTTP — cache MISS (3 runs)
// ---------------------------------------------------------------------------
section('FULL HTTP — cache MISS (3 runs, median)');

Cache::tags(['campaigns'])->flush();
Cache::forget('campaigns:count:v4:all');

$missHttpSamples = [];
$missQuerySamples = [];
for ($i = 0; $i < 3; $i++) {
    Cache::tags(['campaigns'])->flush();
    Cache::forget('campaigns:count:v4:all');
    $result = runHttp($app, makeRequest(), $queries);
    $missHttpSamples[] = $result['ms'];
    $missQuerySamples[] = $result['query_ms'];
}

$missHttpMedian = median($missHttpSamples);
$missQueryMedian = median($missQuerySamples);

// ---------------------------------------------------------------------------
// Breakdown summary
// ---------------------------------------------------------------------------
section('BREAKDOWN — cache HIT (median, measured data)');

$rows = [
    ['1. Laravel bootstrap (one-time script)', $bootstrapMs, null],
    ['2. Middleware + routing (est.)', $middlewareMs, 'HTTP - cache - json - mysql'],
    ['3. Rate limiter Redis', $rateMedian, 'isolated RateLimiter::attempt'],
    ['4. Cache lookup Redis', $cacheMedian, 'isolated Cache::tags get'],
    ['5. MySQL queries', $queryMedian, 'during HTTP, should be ~0'],
    ['6. Hydration model', 0.0, 'skipped on cache HIT path'],
    ['7. JSON serialization (est.)', $jsonMedian, 're-encode response body'],
    ['8. HTTP kernel total', $httpMedian, 'full request end-to-end'],
];

echo "\n  #  Component                              Time (ms)   % HTTP\n";
echo "  " . str_repeat('-', 66) . "\n";
foreach ($rows as [$label, $time, $note]) {
    if ($label === '1. Laravel bootstrap (one-time script)') {
        printf("  %-40s %8.3f ms   %s\n", $label, $time, 'one-time per PHP process');
        continue;
    }
    $pct = $httpMedian > 0 ? round(($time / $httpMedian) * 100, 1) : 0;
    $extra = $note ? " ({$note})" : '';
    printf("  %-40s %8.3f ms %5.1f%%%s\n", $label, $time, $pct, $extra);
}

section('BREAKDOWN — cache MISS (median, measured data)');

$missMiddleware = max(0.0, round($missHttpMedian - $missQueryMedian - $mysqlMs - $hydrationMs - $jsonMs, 3));

$missRows = [
    ['Middleware + routing (est.)', $missMiddleware],
    ['Rate limiter Redis', $rateMedian],
    ['Cache lookup Redis (miss)', median(array_map(static function () use ($app, &$queries): float {
        Cache::tags(['campaigns'])->flush();
        $t = microtime(true);
        try {
            Cache::tags(['campaigns'])->get('campaigns:all:v4:per_page:15:page:1');
        } catch (\BadMethodCallException) {
            Cache::get('campaigns:all:v4:per_page:15:page:1');
        }

        return ms($t);
    }, range(1, 1)))],
    ['MySQL queries', $missQueryMedian],
    ['Hydration + cache write (est.)', max(0.0, $repoTotalMs - $mysqlMs)],
    ['JSON serialization', $jsonMs],
    ['HTTP kernel total', $missHttpMedian],
];

echo "\n  Component                              Time (ms)\n";
echo "  " . str_repeat('-', 50) . "\n";
foreach ($missRows as [$label, $time]) {
    printf("  %-40s %8.3f ms\n", $label, $time);
}

// ---------------------------------------------------------------------------
// Query log — cache miss
// ---------------------------------------------------------------------------
section('QUERY LOG — CACHE MISS (repository path)');

foreach ($missQueries as $i => $q) {
    echo "\n  Query #" . ($i + 1) . " — {$q['time_ms']} ms\n";
    echo '  SQL: ' . $q['sql'] . "\n";
    if ($q['bindings'] !== []) {
        echo '  Bindings: ' . json_encode($q['bindings']) . "\n";
    }
}

// ---------------------------------------------------------------------------
// Response metadata + external HTTP timing note
// ---------------------------------------------------------------------------
section('RESPONSE METADATA');

if ($lastResponse instanceof Response) {
    echo "  HTTP status: {$lastResponse->getStatusCode()}\n";
    echo '  Response bytes: ' . strlen($lastResponse->getContent() ?: '') . "\n";
    echo '  X-RateLimit-Limit: ' . ($lastResponse->headers->get('X-RateLimit-Limit') ?? 'n/a') . "\n";
    echo '  X-RateLimit-Remaining: ' . ($lastResponse->headers->get('X-RateLimit-Remaining') ?? 'n/a') . "\n";
}

echo "\nDone.\n";
