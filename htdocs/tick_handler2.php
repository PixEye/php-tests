<?php
include_once 'Include/head.php';
if(!isSet($argv)) echo "\t<pre>";

// see: http://fr.php.net/manual/fr/control-structures.declare.php
function tick_handler()
{
  echo "tick_handler() called", PHP_EOL;
}

$a = 1;
tick_handler();

if ($a > 0) {
    $a += 2;
    tick_handler();
    print($a.PHP_EOL);
    tick_handler();
}
tick_handler();

if(!isSet($argv)) echo '</pre>';
include_once 'Include/tail.php';
