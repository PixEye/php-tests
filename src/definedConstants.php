<?php
include_once 'Include/head.php';

$n = count(get_defined_constants());
echo "\t<p>There are $n constants defined on this system.</p>", PHP_EOL;

echo '<pre>';
define('MY_CONSTANT', 1);
var_export(get_defined_constants(true));
echo '</pre>', PHP_EOL;

include_once 'Include/tail.php';
?>
