<?php
@include 'Include/head.php';

$line = 'R*    0.0.0.0/0 [120/1] via 10.20.20.1, 00:00:16, Vlan1';
$marker = '0.0.0.0/0';

$r = strPos($line, $marker);

echo 'r = ', var_export($r, 1), PHP_EOL;

@include 'Include/tail.php';
