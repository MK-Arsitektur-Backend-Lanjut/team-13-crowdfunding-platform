<?php
/**
 * HTTP Profiling: measure via nginx/PHP-FPM
 * Run: docker exec crowdfunding_app php http-profile.php
 */
$base = 'http://nginx';

function measure(string $url, int $times = 1): array {
    $results = [];
    for ($i = 0; $i < $times; $i++) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $t0 = microtime(true);
        $body = curl_exec($ch);
        $t1 = microtime(true);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $results[] = [
            'code' => $info['http_code'],
            'ms' => round(($t1 - $t0) * 1000, 2),
            'size' => strlen($body),
        ];
        usleep(100000); // 100ms between requests
    }
    return $results;
}

function stats(array $results): array {
    $ms = array_column($results, 'ms');
    $min = min($ms);
    $max = max($ms);
    $avg = array_sum($ms) / count($ms);
    sort($ms);
    $med = $ms[(int)(count($ms) / 2)];
    return ['min' => $min, 'max' => $max, 'avg' => $avg, 'med' => $med];
}

echo str_repeat('=', 70) . "\n";
echo "  HTTP PROFILING via PHP-FPM\n";
echo str_repeat('=', 70) . "\n\n";

echo "  Warming up (3x campaigns)...\n";
measure($base . '/api/campaigns', 3);

echo "  1. Health check (/up) - 3 runs\n";
$h = measure($base . '/up', 3);
$hs = stats($h);
foreach ($h as $r) echo "     HTTP {$r['code']}: {$r['ms']} ms, {$r['size']} bytes\n";
echo "     -> avg {$hs['avg']} ms, min {$hs['min']} ms, max {$hs['max']} ms\n\n";

echo "  2. Campaigns list - 5 runs\n";
$c = measure($base . '/api/campaigns', 5);
$cs = stats($c);
foreach ($c as $r) echo "     HTTP {$r['code']}: {$r['ms']} ms, {$r['size']} bytes\n";
echo "     -> avg {$cs['avg']} ms, min {$cs['min']} ms, max {$cs['max']} ms\n\n";

echo "  3. Campaigns filter by status (aktif) - 3 runs\n";
$s = measure($base . '/api/campaigns/status/aktif', 3);
$ss = stats($s);
foreach ($s as $r) echo "     HTTP {$r['code']}: {$r['ms']} ms, {$r['size']} bytes\n";
echo "     -> avg {$ss['avg']} ms, min {$ss['min']} ms, max {$ss['max']} ms\n\n";

echo "  4. Campaigns filter by status (selesai) - 3 runs\n";
$s2 = measure($base . '/api/campaigns/status/selesai', 3);
$s2s = stats($s2);
foreach ($s2 as $r) echo "     HTTP {$r['code']}: {$r['ms']} ms, {$r['size']} bytes\n";
echo "     -> avg {$s2s['avg']} ms, min {$s2s['min']} ms, max {$s2s['max']} ms\n\n";

echo str_repeat('-', 50) . "\n";
echo "  BREAKDOWN ESTIMATION:\n\n";

$bootMs = $hs['avg'];
$appMs = $cs['avg'] - $bootMs;
$filterMs = $ss['avg'] - $bootMs;
$filter2Ms = $s2s['avg'] - $bootMs;

echo "  Bootstrap + Framework overhead  : ~{$bootMs} ms\n";
if ($appMs > 0) echo "  Application logic (controller)   : ~" . round($appMs, 2) . " ms\n";
if ($filterMs > 0) echo "  Filter logic (status query)      : ~" . round($filterMs, 2) . " ms\n";
echo "\n";
echo "  Bootstrap % of total:\n";
if ($cs['avg'] > 0) echo "    Campaigns list  : " . round(($bootMs / $cs['avg']) * 100, 1) . "%\n";
if ($ss['avg'] > 0) echo "    Filter (aktif)  : " . round(($bootMs / $ss['avg']) * 100, 1) . "%\n";

echo "\n" . str_repeat('=', 70) . "\n";