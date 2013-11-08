#!/usr/bin/env php
<?php
/**
 * Created on 2013-10-30 by Julien Moreau
 *
 * Last commit of this file:
 * $Id$
 */

$cmd = basename(__FILE__);

if (!function_exists('json_decode'))
{
	$err_msg= 'The json_decode() function is not available!';
	if (!isSet($argv))
		error_log('In '.__FILE__.' at line '.__LINE__.": $err_msg");
	die($err_msg.PHP_EOL);
}

if (!isSet($argv))
	die("<br/>\n".'This is a command line script!');

if (count($argv)<=1)
	die("Usage:\n\t\$ php $cmd <json_file>".PHP_EOL);

$input_file = $argv[1];
$base_input_file = basename($input_file);
echo "Parsing '$input_file'...", PHP_EOL;
$json = file_get_contents($input_file);

$Items = json_decode($json, true);
if (!isSet($Items))
{
	switch (json_last_error()) {
		case JSON_ERROR_NONE:
			$err = 'No errors';
		break;
		case JSON_ERROR_DEPTH:
			$err = 'Maximum stack depth exceeded';
		break;
		case JSON_ERROR_STATE_MISMATCH:
			$err = 'Underflow or the modes mismatch';
		break;
		case JSON_ERROR_CTRL_CHAR:
			$err = 'Unexpected control character found';
		break;
		case JSON_ERROR_SYNTAX:
			$err = 'Syntax error, malformed JSON';
		break;
		case JSON_ERROR_UTF8:
			$err = 'Malformed UTF-8 characters, possibly incorrectly encoded';
		break;
		default:
			$err = 'Unknown error';
		break;
	}
	die("Unable to json_decode() '$input_file'! $err".PHP_EOL);
}

var_export($Items);
