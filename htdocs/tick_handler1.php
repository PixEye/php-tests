<?php
include_once 'Include/head.php';
if(!isSet($argv)) echo "\t<pre>";

declare(ticks=1);

// see: http://fr.php.net/manual/fr/control-structures.declare.php
function tick_handler() // A function called on each tick event
{
    echo "tick_handler() called", PHP_EOL;
}

register_tick_function('tick_handler');

$a = 1;

if ($a > 0) {
    $a += 2;
    print($a.PHP_EOL);
}

unregister_tick_function('tick_handler');
if(!isSet($argv)) echo '</pre>';
include_once 'Include/tail.php';
