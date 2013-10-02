#!/usr/bin/env php
<?php
$small_options = 'dhn:qst';
$long_options = Array('debug', 'help', 'quiet', 'safe', 'test');

if (isSet($_SERVER['HTTP_HOST']))
	echo '<pre>_SERVER["HTTP_HOST"] = '.$_SERVER['HTTP_HOST'].PHP_EOL;

if (!isSet($argc)) $argc = 'NULL';
echo "argc = $argc", PHP_EOL;

echo 'argv = ',
var_export($argv);
echo PHP_EOL;

$Opt = getOpt($small_options, $long_options);
echo 'Opt = ';
var_export($Opt);

if (isSet($_SERVER['HTTP_HOST'])) echo '</pre>';

echo PHP_EOL;
