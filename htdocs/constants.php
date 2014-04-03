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

is_readable('Include/head.php') && require_once 'Include/head.php';
define('NL', PHP_EOL);

$mirror = subStr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if ('en'==$mirror) {
    $mirror = 'www';
}
?>
	<div>See:
<?php
$sep = ','.NL;
$Lg = array('en', 'fr');
$iStyle = 'style="height:12px; margin-left:4px"'.NL;
forEach ($Lg as $lg) {
    $base_url = "http://$mirror.php.net/manual/$lg";
    ?>
        <a href="<?php echo $base_url?>/language.constants.predefined.php"
            >PHP predefined constants<img alt="<?php echo $lg?>"
            <?php echo $iStyle?>
            src="../../img/flag-<?php echo $lg?>.gif"/></a>,
        <a href="<?php echo $base_url?>/reserved.constants.php"
            >PHP reserved constants<img alt="<?php echo $lg?>"
            <?php echo $iStyle?>
            src="../../img/flag-<?php echo $lg?>.gif"/></a>,
        <a href="<?php echo $base_url?>/dir.constants.php"
            >PHP manual for dir constants<img alt="<?php echo $lg?>"
            <?php echo $iStyle?>
            src="../../img/flag-<?php echo $lg?>.gif"/></a><?php echo $sep?>
<?php
    $sep = '';
}
?>.</div>
	<div><br/></div>
	<div style="float:left; width:49%; margin-right:1%">
<?php
echo "\t<div><b>__CLASS__</b> = <tt>",
    var_export(__CLASS__), '</tt></div>', NL;
if (defined('PHP_VERSION_ID') && PHP_VERSION_ID>=50300) {
    echo "\t<div><b>__DIR__</b> = <tt>",
        var_export(__DIR__), '</tt></div>', NL;
}
echo "\t<div><b>__FILE__</b> = <tt>",
    var_export(__FILE__), '</tt></div>', NL;
echo "\t<div><b>__FUNCTION__</b> = <tt>",
    var_export(__FUNCTION__), '</tt></div>', NL;
echo "\t<div><b>__LINE__</b> = <tt>",
    var_export(__LINE__), '</tt></div>', NL;
echo "\t<div><b>__METHOD__</b> = <tt>",
    var_export(__METHOD__), '</tt></div>', NL;
if (defined('PHP_VERSION_ID') && PHP_VERSION_ID>=50300) {
    echo "\t<div><b>__NAMESPACE__</b> = <tt>",
        var_export(__NAMESPACE__), '</tt></div>', NL;
}
if (defined('PHP_VERSION_ID') && PHP_VERSION_ID>=50400) {
    echo "\t<div><b>__TRAIT__</b> = <tt>",
        var_export(__TRAIT__), '</tt></div>', NL;
}
?>
	</div>
	<div><br/></div>
<?php
$a = array(
    '__FILE__', 'DIRECTORY_SEPARATOR', 'PATH_SEPARATOR',
    'PHP_OS', 'PHP_SYSCONFDIR', 'PHP_VERSION', 'PHP_VERSION_ID',
    'SCANDIR_SORT_ASCENDING', 'SCANDIR_SORT_DESCENDING', 'SCANDIR_SORT_NONE'
);
forEach ($a as $c) {
    if (defined($c)) {
        echo "\t<div><b>$c</b> = <tt>", var_export(constant($c)), '</tt></div>', NL;
    }
}
?>
	<div><br style="clear:both"/></div> <!-- -------------------------- -->
<?php
echo "\t<pre style=\"width:48%; float:left\">get_defined_constants(true) = ";
var_export(get_defined_constants(true));
echo '</pre>', NL;

$tmp = get_defined_constants(false); ksort($tmp);
echo "\t<pre style=\"width:48%; float:left\">get_defined_constants(false) = ";
var_export($tmp);
echo '</pre>', NL;

is_readable('Include/tail.php') && require_once 'Include/tail.php';

// Vim editing preferences (PHP_CodeSniffer compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
