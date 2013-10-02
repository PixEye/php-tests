#!/usr/bin/env php
<?php
/**
 * @author Julien Moreau
 * @since  2013-09-30
 *
 * This script decodes base64 strings: argument(s) or standard input (STDIN).
 * It should be executable (chmod +x).
 *
 * Examples:
 *	$ /path/to/base64decode.php SW52aXTDqXM=
 *	$ /path/to/base64decode.php < file.ldif
 */

if(!isSet($argv)) { // if not called from command line
	include_once 'Include/head.php';
	$msg = 'Warning: this script is supposed to be called from the command line!';
	echo "\t<pre>$msg</pre>", PHP_EOL;
	include_once 'Include/tail.php';
	exit(0);
}

$mark = ':: ';	// LDAP base64 marker (LDIF syntax)

if(isSet($argv[1])) {
	// Decode argument(s)
	# $input = $argv[1];
	$cmd = array_shift($argv);
	$input = implode(' ', $argv);
	if (false!==($pos=strPos($input, $mark))) {
		$prefix = subStr($input, 0, $pos);
		$input  = subStr($input, $pos);
		echo "$prefix: ", base64_decode($input), PHP_EOL;
	} else
		echo base64_decode($input), PHP_EOL;
}
else
	// Filter mode: decode STDIN
	while($input=fgets(STDIN))
		if (false!==($pos=strPos($input, $mark))) {
			$prefix = subStr($input, 0, $pos);
			$input  = subStr($input, $pos);
			echo "$prefix: ", base64_decode($input), PHP_EOL;
		} else echo $input; // End of line is already in $input

# vim: tabstop=8 shiftwidth=8 noexpandtab
