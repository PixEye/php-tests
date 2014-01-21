#!/usr/bin/env php
<?php
/**
 * Created on 2013-11-08 by Julien Moreau
 *
 * Last commit of this file:
 * $Id$
 */

$cmd = basename(__FILE__);

if (!function_exists('unserialize'))
{
	$err_msg= 'The unserialize() function is not available!';
	if (!isSet($argv))
		error_log('In '.__FILE__.' at line '.__LINE__.": $err_msg");
	die($err_msg.PHP_EOL);
}

if (!isSet($argv))
	die("<br/>\n".'This is a command line script!');

if (count($argv)<=1)
	die("Usage:\n\t\$ php $cmd <serialized_file>".PHP_EOL);

$input_file = $argv[1];
$base_input_file = basename($input_file);
echo "Parsing '$input_file'...", PHP_EOL;
$serialized = trim(file_get_contents($input_file));

$decoded = unserialize($serialized);
if (false===$decoded)
	die("Unable to unserialize() '$input_file'!".PHP_EOL);

var_export($decoded);
