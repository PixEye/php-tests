<?php
/**
 * Last commit of this file (GMT):
 * $Id$
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PhpTests
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 PixEye.net
 * @license   Affero GPL http://choosealicense.com/licenses/agpl/
 * @version   GIT: $Revision$
 * @link      https://github.com/PixEye/php-tests
 * @since     Local time: $Date$
 * @filesource
 */

is_readable('Include/head.php') && require_once 'Include/head.php';

$a1 = array('a' => 'green', 'red', 'blue', 'red');
print('<pre>a1 = ');
var_export($a1);

$a2 = array('b' => 'green', 'yellow', 'red');
print("\na2 = ");
var_export($a2);

$result = array_diff($a1, $a2);
print("</pre>\n<hr/>\n<pre>array_diff(a1, a2)  = a1 - a2 = ");
var_export($result);

$result = array_diff($a2, $a1);
print("</pre>\n<hr/>\n<pre>array_diff(a2, a1) = a2 - a1 = ");
var_export($result);

print("</pre>\n");

is_readable('Include/tail.php') && require_once 'Include/tail.php';

// Vim editing preferences (PHP_CodeSniffer compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
