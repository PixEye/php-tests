<?php
/**
 * Compute brightness & inverse colors of a given color.
 *
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

$charset = 'utf-8';
$title = 'Compute shine percentage & opposite color';
file_exists('Include/head.php') && require_once 'Include/head.php';
?>
	<h2>Some examples:</h2>

<?php
$DEBUG = false;
$colors = array(
    'FF701D' => 'orange', 'E1EF21' => 'yellow',
    '0FA5B3' => 'teal', '338DC3' => 'blue', 'B3C1DD' => 'cyan',
    'A7ABAC' => 'gray', 'FFFFFF' => 'white', '000000' => 'black'
);

/**
 * Retourne la chaine négative de la couleur passée en paramètre :
 *
 * @param string $color_code Hexadecimal color code (without hash)
 *
 * @return string            The opposite hexadecimal color code
 */
function negate($color_code)
{
    $ret = '';
    $L = strLen($color_code);
    for ($i = 0; $i<$L; $i++) {
        $origin = base_convert($color_code[$i], 16, 10);
        $newNb  = 15-$origin;
        $ret   .= base_convert($newNb, 10, 16);
    }

    return $ret;
}

/**
 * Compute the shine percentage of a color (0 for black & 100 for white).
 *
 * @param string $color_code Hexadecimal color code (without hash)
 *
 * @return string            The shine percentage of this color
 */
function shine($color_code)
{
    global $DEBUG;

    $light = 0;
    $L = strLen($color_code);
    if ($L!=3 && $L!=6) {
        return 0;
    }

    $token = $L/3;        // $token is 1 or 2 (nb of char/color)
    echo $DEBUG?"<!-- token = $token -->\n":'';
    $max = pow(16, $token);    // $max is 16 or 256
    echo $DEBUG?"<!-- max = $max -->\n":'';

    $c = 0;
    for ($i = 1; $i<=3; $i++) {    // $i is the RGB index
        for ($j = $token-1; $j>=0; $j--) {    // $j is 1 or 2
            $char = $color_code[$c++];
            echo $DEBUG?"<!-- char = $char -->\n":'';
            $p = pow(16, $j);
            $addon = $p*base_convert($char, 16, 10);
            echo $DEBUG?"<!-- addon = $addon -->\n":'';
            $light+=$addon;
        }
    }

    echo $DEBUG?"<!-- shine($color_code) = $light -->\n":'';
    $percent = round($light*100/($max*3));
    echo $DEBUG?"<!-- shine($color_code) = $percent% -->\n":'';

    return $percent;
}

$p_threshold = 50;    // Threshold percentage between black & white

forEach ($colors as $color_code => $color_name) {
    $b = shine($color_code);
    $fg = ($b>$p_threshold)?'Black':'White';
    $wob = (50-$b<$p_threshold)?'White':'Black';
    $negative_col = strToUpper(negate($color_code));

    echo "\t<p class=\"barre\" style=\"color:$fg; background:#$color_code;";
    echo " border-color:#$color_code\">\n\t  <span class=\"monospace\">";
    echo "$color_code</span>&nbsp;=&gt; $color_name (shine: $b%)\n";
    echo "\t  <small style=\"color:$wob; background:#$negative_col\">";
    echo "( opposite color: <span class=\"monospace\">$negative_col</span>";
    echo " )</small></p>\n";
	echo "\t<p style=\"background:$color_name\">$color_name</p>\n";
}
?>

	<h2>Standard colors:</h2>

	<p><a href="http://www.w3schools.com/html/html_colornames.asp"
	  >Source @W3schools</a></p>

<?php
$std_colors = array(
	'aqua', 'blue', 'fuchsia', 'gray', 'green', 'lime', 'maroon',
	'navy', 'olive', 'orange', 'purple', 'red', 'silver', 'teal', 'yellow'
);

forEach ($std_colors as $color_name) {
	echo "\t<span style=\"padding:2px 4px; background:$color_name\">$color_name\n";
    echo "\t  =<span style=\"color:white\">= $color_name</span></span>\n";
}

file_exists('Include/tail.php') && require_once 'Include/tail.php';

// vim: tabstop=4 shiftwidth=4 expandtab
