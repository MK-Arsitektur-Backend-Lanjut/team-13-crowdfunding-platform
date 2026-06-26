<?php
/**
 ====================================================================
 PROFILING VIA PHP-FPM (HTTP)
 ====================================================================
 Strategy: hit via HTTP, measure on the PHP side using a marker file
 that gets created by the request itself.

 We'll send a request with a special query param that triggers
 timing, then read the result.
 ====================================================================
 */

declare(strict_types=1);

$baseUrl = 'http://nginx:80';

function httpTimed(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    
    $t0 = microtime(true);
    $response = curl_exec($ch);
    $t1 = microtime(true);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000;
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($response, $headerSize);
    curl_close($ch);
    
    return [
        'code'     => $httpCode,
        'total_ms' => round($totalTime, 3),
        'body'     => $body,
        'size'     => strlen($body),
    ];
}

echo str_repeat('=', 75) . "\n";
echo "  PROFILING VIA HTTP (PHP-FPM) — WARM\n";
echo str_repeat('=', 75) . "\n\n";

// Run 3 warmup requests
echo "  Warming up...\n";
for ($i = 0; $i < 3; $i++) {
    httpTimed($baseUrl . '/api/campaigns');
}

echo "  Profiling...\n\n";

// Run 5 timed requests
$runs = [];
for ($i = 0; $i < 5; $i++) {
    $result = httpTimed($baseUrl . '/api/campaigns');
    $runs[] = $result;
    echo sprintf("  Run %d: HTTP %d, %8.3f ms, %d bytes\n",
        $i + 1, $result['code'], $result['total_ms'], $result['size']);
}

// Stats
$times = array_column($runs, 'total_ms');
echo "\n" . str_repeat('-', 50) . "\n";
echo sprintf("  Min     : %8.3f ms\n", min($times));
echo sprintf("  Max     : %8.3f ms\n", max($times));
echo sprintf("  Mean    : %8.3f ms\n", array_sum($times) / count($times));
sort($times);
$mid = (int) (count($times) / 2);
echo sprintf("  Median  : %8.3f ms\n", $times[$mid]);

echo "\n" . str_repeat('=', 75) . "\n";

// Now measure breakdown via HTTP with _debug parameter
// We'll add special logging in the controller/repository

echo "\n  BREAKDOWNS:\n";
echo str_repeat('-', 50) . "\n";

// Measure empty response (health check)
$h = httpTimed($baseUrl . '/up');
echo sprintf("  Health check (/:  %8.3f ms (baseline bootstrap)\n", $h['total_ms']);

// Measure campaigns
$c = httpTimed($baseUrl . '/api/campaigns');
echo sprintf("  Campaigns list  : %8.3f ms\n", $c['total_ms']);

// Measure by status
$s = httpTimed($baseUrl . '/api/campaigns/status/aktif');
echo sprintf("  Filter aktif    : %8.3f ms\n", $s['total_ms']);

// Estimate overhead
echo "\n  ESTIMASI OVERHEAD:\n";
echo str_repeat('-', 50) . "\n";
echo sprintf("  Bootstrap overhead (health): %8.3f ms\n", $h['total_ms']);
echo sprintf("  Full request w/ cache HIT  : %8.3f ms\n", $c['total_ms']);
if ($h['total_ms'] > 0 && $c['total_ms'] > $h['total_ms']) {
    echo sprintf("  Application logic           : %8.3f ms\n", $c['total_ms'] - $h['total_ms']);
}

echo "\n" . str_repeat('=', 75) . "\n";