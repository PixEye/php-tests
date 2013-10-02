<?php
include_once 'Include/head.php';
if (!isSet($argv)) echo '<pre>';

$X = Array(
	null,
	false,
	0,
	'',
	'toto',
	Array(),
	Array(''),
	Array('a'),
	Array('a' => ''),
);

forEach($X as $x)
{
	#echo PHP_EOL;
	echo 'x=', var_export($x, 1);

/*	if (''==trim($x))
		echo "trim(x) is ''";
	else
		echo "trim(x) iso NOT ''";
	echo PHP_EOL, ' & ';	*/

	echo ' and is ';
	if (empty($x))
		echo 'empty';
	else
		echo 'NOT empty';

	echo PHP_EOL;
}

if(!isSet($argv)) echo '</pre>';
echo PHP_EOL;
include_once 'Include/tail.php';
