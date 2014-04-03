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

is_readable('error-handler.php') && require_once 'error-handler.php';
is_readable('Include/head.php')  && require_once 'Include/head.php';
$self = basename($_SERVER['PHP_SELF']);

// Important (because of head.php):
if (isSet($lg) && strLen($lg)==2) {
    if ($lg=='en') {
        $lg = 'en_US';
    } else {
        $lg = $lg.'_'.strtoupper($lg).'@euro';
    }
}

// Get languages supported by the system:
$SupportedLg = explode("\n", trim(`locale -a`));

$nb_supported_lg = count($SupportedLg);

// Display supported languages if in debug mode:
echo "  <!-- $nb_supported_lg supported languages",
    " (from 'locales -a')";
if (isSet($DEBUG) && $DEBUG) {
    echo " : "; print_r($SupportedLg);
}
echo " -->\n";

$DEBUG = true;        // active debug mode

// Language detection from the web user agent preferences:
if (!isSet($lg) || ''==trim($lg)) {
    $lg = 'en_US';        // Default language
    $HTTP_ACCEPT_LANGUAGE = $_SERVER[HTTP_ACCEPT_LANGUAGE];
    echo $DEBUG?"<!-- HTTP_ACCEPT_LANGUAGE=\"$HTTP_ACCEPT_LANGUAGE\" -->\n":'';
    $AcceptLg = explode(',', $HTTP_ACCEPT_LANGUAGE);

    if (is_array($SupportedLg)) {
        forEach ($AcceptLg as $lgt) {
            echo $DEBUG?"<!-- lgt = $lgt -->\n":'';
            $pos = strpos($lgt, ';');
            if ($pos!==false) {
                $lgt = subStr($lgt, 0, $pos);
            }
            echo $DEBUG?"<!-- lgt = $lgt -->\n":'';
            $lgt = subStr(trim($lgt), 0, 5);
            if (strlen($lgt)>2 && $lgt[2]=='-') {
                $lgt[2] = '_';
                $lgt[3] = strtoupper($lgt[3]);
                $lgt[4] = strtoupper($lgt[4]);
                // if (!strstr($lgt, '@')) $lgt.='@euro';
            }
            echo $DEBUG?"<!-- lgt = $lgt -->\n":'';
            if (in_Array($lgt, $SupportedLg)) {
                $lg = $lgt; break;
            }
        }
    } elseIf (count($AcceptLg)>0) {
        $lg = $AcceptLg[0];
    }
    echo $DEBUG?"    <!-- lg = '$lg' -->\n":'';
} else {
    echo $DEBUG?"  <!-- lg was already set to : '$lg' -->\n":'';
}

switch(subStr($lg, 0, 2)) {
case 'fr':
    $msg = 'Cliquez sur le bouton de votre choix.';
    break;
default:    $lg = 'en';
    $msg = 'Click on the button of your choice.';
}
?>
    <script type="text/javascript">
        <!--
            alert(confirm('<?php echo $msg?>'));
        -->
    </script>
<?php setLocale(LC_ALL, $lg); ?>
    <div style="float:left"><?php echo GMStrFTime('%c')?></div>
<?php
is_readable('Include/tail.php') && require_once 'Include/tail.php';

// Vim editing preferences (PHP_CodeSniffer compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
