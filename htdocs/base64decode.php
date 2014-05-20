#!/usr/bin/env php
<?php
/**
 * This script decodes base64 strings: argument(s) or standard input (STDIN).
 * It should be executable (chmod +x).
 *
 * Examples:
 *  $ /path/to/base64decode.php SW52aXTDqXM=
 *  $ /path/to/base64decode.php < file.b64
 *
 * Last commit of this file (GMT):
 * $Id$
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PhpTests
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 VeePee
 * @license   http://choosealicense.com/licenses/no-license/ No license
 * @version   GIT: $Revision$
 * @link      https://github.com/PixEye/php-tests
 * @since     2013-09-30
 * @filesource
 */

if (!isSet($argv)) { // if not called from command line
    is_readable('Include/head.php') && include_once 'Include/head.php';
    $msg = 'Warning: this script is supposed to be called from the CLI!';
    echo "\t<pre>$msg</pre>", PHP_EOL;
    is_readable('Include/tail.php') && include_once 'Include/tail.php';
    exit(0);
}

if (isSet($argv[1])) {
    if ('-h'===$argv[1] || '--help'===$argv[1]) {
        $cmd = basename($argv[0]);
        fprintf(STDERR, 'Usage 1: %s [<b64_string>]%s', $cmd, PHP_EOL);
        fprintf(STDERR, 'Usage 2: %s < file.b64%s', $cmd, PHP_EOL);
        exit(1);
    }

    // Decode argument(s)
    // $input = $argv[1];
    $cmd = array_shift($argv);
    $input = implode(' ', $argv);
    echo base64_decode($input), PHP_EOL;
} else {
    // Filter mode: decode STDIN
    while ($input=fgets(STDIN)) {
        echo base64_decode($input);
    }
}

// Vim editing preferences (phpcs compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
