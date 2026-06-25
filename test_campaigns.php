<?php
$_SERVER['REQUEST_URI'] = '/campaigns';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
ob_start();
require '/var/www/html/public/index.php';
$o = ob_get_clean();
echo substr($o, 0, 500);
echo "\n...LENGTH: " . strlen($o) . "\n";