#!/usr/bin/env php
<?php
/**
 * This script decodes base64 strings: argument(s) or standard input (STDIN).
 * It should be executable (chmod +x).
 *
 * Examples:
 *  $ /path/to/base64decode.php SW52aXTDqXM=
 *  $ /path/to/base64decode.php < file.ldif
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PhpTests
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 VeePee
 * @license   http://choosealicense.com/licenses/no-license/ No license
 * @version   SVN: $Revision$
 * @link      https://github.com/PixEye/php-tests
 * @since     2013-09-30
 * @filesource
 */

if (!isSet($argv)) { // if not called from command line
    file_exists('Include/head.php') && include_once 'Include/head.php';
    $msg = 'Warning: this script is supposed to be called from the CLI!';
    echo "\t<pre>$msg</pre>", PHP_EOL;
    file_exists('Include/tail.php') && include_once 'Include/tail.php';
    exit(0);
}

$mark = ':: ';    // LDAP base64 marker (LDIF syntax)

if (isSet($argv[1])) {
    if ('-h'===$argv[1] || '--help'===$argv[1]) {
        $cmd = basename($argv[0]);
        fprintf(STDERR, 'Usage 1: %s [<b64_string>]%s', $cmd, PHP_EOL);
        fprintf(STDERR, 'Usage 2: %s < file.ldif%s', $cmd, PHP_EOL);
        exit(1);
    }

    // Decode argument(s)
    // $input = $argv[1];
    $cmd = array_shift($argv);
    $input = implode(' ', $argv);
    if (false!==($pos=strPos($input, $mark))) {
        $prefix = subStr($input, 0, $pos);
        $input  = subStr($input, $pos);
        echo "$prefix: ", base64_decode($input), PHP_EOL;
    } else {
        echo base64_decode($input), PHP_EOL;
    }
} else {
    // Filter mode: decode STDIN
    while ($input=fgets(STDIN)) {
        if (false!==($pos=strPos($input, $mark))) {
            $prefix = subStr($input, 0, $pos);
            $input  = subStr($input, $pos);
            echo "$prefix: ", base64_decode($input), PHP_EOL;
        } else {
            echo $input; // End of line is already in $input
        }
    }
}

// Vim editing preferences (phpcs compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
