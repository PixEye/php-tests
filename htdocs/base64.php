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

$charset = 'UTF-8';
file_exists('Include/head.php') && require_once 'Include/head.php';
$source = isSet($_REQUEST['source']) ? trim($_REQUEST['source']) : '';
$srcLen = strlen($source);
?>
	<p><br/></p>
  <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
	  <div class="center">
	    <label>Source string to encode or decode:<br/>
        <textarea cols="100" rows="15" name="source"><?php echo $source?></textarea>
	    </label><br/><br/>
	    <input type="reset" value="Reset"/>
	    <input type="submit" value="Encode" name="encode"/>
	    <input type="submit" value="Decode" name="decode"/>
<?php
if ($srcLen>0) {
    echo "\t    <p>This string is $srcLen characters long.</p>\n\n";
    // echo "<pre>_REQUEST = "; print_r($_REQUEST); echo "</pre>\n";
    if (isSet($_REQUEST['encode'])) {
        $type = 'Encoded';
        $result = base64_encode($source);
        $resLen = strlen($result);
    } else {
        $type = 'Decoded';
        $result = base64_decode($source);
        $resLen = strlen($result);
        // Style & security:
        $result = str_replace('<br>', "<br/>\n", $result);
        $result = htmlSpecialChars($result);
    }
    // $result = nl2br($result);
    echo "\t    <p><b>$type</b> string is:</p>\n\n";
    echo "\t    <textarea cols=\"100\" rows=\"15\" name=\"result\">";
    echo "$result</textarea>\n\n";
    echo "\t    <p>This string is $resLen characters long.";
    if ($resLen==16) {
        if ($result==md5('toto', true)) {
            echo "<br/>\n\t     Match found! :)";
        }
        echo "</p>\n";
        $Tmp = unpack('H*', $result);
        echo "<pre>unpack('H*', $result) = "; var_dump($Tmp); echo "</pre>\n";
    } else {
        echo "</p>\n";
    }
}
?>
	  </div>
	</form>
<?php
file_exists('Include/tail.php') && require_once 'Include/tail.php';

// vim: tabstop=4 shiftwidth=4 expandtab
