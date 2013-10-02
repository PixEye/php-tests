<?php
$charset = 'UTF-8';
# @include_once 'Include/head.php';
if (isSet($_SERVER['HTTP_HOST'])) print("\t<pre>");
if (defined(PHP_EOL)) $eol = PHP_EOL; else $eol = "\n"; // end of line
$Value = array('"', 'php', "aujoud'hui", "Sébastien €", utf8_decode("Sébastien €"));
forEach($Value as $value)
{
	print($eol.'Serialized   : ');
	$s = serialize($value);
	var_dump($s);

	$z = unserialize($s);
	print('Unserialized : ');
	var_dump($z);

	if ($z===$value) $t = 'ok'; else $t = 'NOK!';
	print("$t - Initial : ");
	var_dump($value);
}
if (isSet($_SERVER['HTTP_HOST'])) print("</pre>\n");
# @include_once 'Include/tail.php';
