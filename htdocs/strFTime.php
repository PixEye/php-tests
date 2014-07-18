<?php
$charset = 'utf-8';
include_once 'Include/head.php';
if(!isSet($argv)) echo "\t<pre>";

if(isSet($argv) && isSet($argv[1]) && is_numeric($argv[1]))
	$stamp = $argv[1];
elseIf (isSet($_REQUEST['stamp']) && ''!=trim($_REQUEST['stamp']))
	$stamp = $_REQUEST['stamp'];
else $stamp = 1380014220;

# $locale = setLocale(LC_ALL, 0);
# echo "locale(LC_ALL) = '$locale'", PHP_EOL;

$locale = setLocale(LC_TIME, 0);
echo "Current LC_TIME locale = '$locale'";
echo ' ; ';
$locale = setLocale(LC_TIME, 'fr_FR.utf8');
echo "new LC_TIME locale = '$locale'.", PHP_EOL;

echo PHP_EOL;

$format = '%c';
# printf("strFTime('%-5s', %s) => %s%s", $format, $stamp, strFTime($format, $stamp), PHP_EOL);
echo "strFTime('$format', $stamp) => ", strFTime($format, $stamp);
echo PHP_EOL;

$format = '%F %X %z';
echo "strFTime('$format', $stamp) => ", strFTime($format, $stamp);

if(!isSet($argv)) echo '</pre>';
echo PHP_EOL;
include_once 'Include/tail.php';
