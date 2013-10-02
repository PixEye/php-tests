<?php
include_once 'Include/head.php';
if(!isSet($argv)) echo "\t<pre>";

if(isSet($argv) && isSet($argv[1]))
	$uptime = $argv[1];
else	$uptime = microtime(true); # 2719635.325337;

$nb_days = floor(($uptime / 86400)*1.0);
$calc1 = $nb_days * 86400;
$calc2 = $uptime - $calc1;
$hour = floor(($calc2 / 3600)*1.0);
$calc3 = $hour * 3600;
$calc4 = $calc2 - $calc3;
$min = floor(($calc4 / 60)*1.0);
$calc5 = $min * 60;
$sec = floor(($calc4 - $calc5)*1.0);

$nb_years = floor($nb_days/365.25); $nb_days = $nb_days%365.25;
if ($nb_years>0) echo "$nb_years year(s), ";

$nb_month = floor($nb_days/30); $nb_days = $nb_days%30;
if ($nb_month>0) echo "$nb_month month(s), ";

printf("$nb_days day(s), %d:%2d:%2d", $hour, $min, $sec);

if(!isSet($argv)) echo '</pre>';
echo PHP_EOL;
include_once 'Include/tail.php';
