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

/**
 * @see http://fr.php.net/manual/fr/language.exceptions.php
 * @see http://www.php.net/~helly/php/ext/spl/classException.html
 */
$Classes = Array(
    'Exception', 'ErrorException',
    // SPL = Standard PHP Library:
    'LogicException', 'BadFunctionCallException', 'BadMethodCallException',
    'DomainException', 'InvalidArgumentException', 'LengthException',
    'OutOfRangeException', 'RuntimeException', 'OutOfBoundsException',
    'OverflowException', 'RangeException', 'UnderflowException',
    // Others:
    'AuthenticationException', 'AuthException'
);

forEach ($Classes as $classe) {
    echo "\t<p>";
    if (class_exists($classe)) {
        echo "<span class=\"success\">The class \"<b>$classe</b>\" exists.</span>";
    } else {
        echo "<span class=\"error\">The class \"$classe\" does not exist.</span>";
    }
    echo "</p>\n";
}

is_readable('Include/tail.php') && require_once 'Include/tail.php';

// Vim editing preferences (PHP_CodeSniffer compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
