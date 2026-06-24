<?php
$r = new Redis();
$connected = $r->connect('redis', 6379, 2);
echo 'Connected: ' . ($connected ? 'YES' : 'NO') . PHP_EOL;
echo 'Ping: ' . $r->ping() . PHP_EOL;
$r->set('test_key', 'hello');
echo 'Get: ' . $r->get('test_key') . PHP_EOL;
echo 'Redis OK from PHP' . PHP_EOL;
