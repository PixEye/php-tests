<?php
include 'Include/head.php';

#function test_return(boolean $is_ok)
function test_return($is_ok)
{
	echo __FUNCTION__, '(', var_export($is_ok), ') is: ';
	return $is_ok?0:1;
}

$Values = Array(TRUE, FALSE);
forEach($Values as $value)
{
	if (!isSet($argv)) echo "\t<pre>";
	echo test_return($value);
	if (!isSet($argv)) echo '</pre>';
	echo PHP_EOL;
}

include 'Include/tail.php';
