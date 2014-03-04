#!/usr/bin/env php
<?php
/**
 * Last commit of this file (GMT):
 * $Id$
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PixShellScripts
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 PixEye.net
 * @license   Affero GPL http://choosealicense.com/licenses/agpl/
 * @version   GIT: $Revision$
 * @link      https://github.com/PixEye/PixShellScripts
 * @since     Local time: $Date$
 * @filesource
 */

$small_options = 'dhn:qst';
$long_options = Array('debug', 'help', 'quiet', 'safe', 'test');

isSet($_SERVER['HTTP_HOST']) &&
    echo '<pre>_SERVER["HTTP_HOST"] = '.$_SERVER['HTTP_HOST'].PHP_EOL;

isSet($argc) || $argc = 'NULL';
echo "argc = $argc", PHP_EOL;

echo 'argv = ',
var_export($argv);
echo PHP_EOL;

$Opt = getOpt($small_options, $long_options);
echo 'Opt = ';
var_export($Opt);

isSet($_SERVER['HTTP_HOST']) && echo '</pre>';

echo PHP_EOL;
