<?php
include_once 'Include/head.php';
if(!isSet($argv)) echo "\t<pre>";

echo "date('c') = ", var_export(date('c'));
if(function_exists('date_default_timezone_get'))
  echo PHP_EOL, "date_default_timezone_get() = ", var_export(date_default_timezone_get());
if(ini_get('date.timezone'))
  echo PHP_EOL, 'date.timezone = ', var_export(ini_get('date.timezone'));

if(!isSet($argv)) echo '</pre>';
echo PHP_EOL;
include_once 'Include/tail.php';
