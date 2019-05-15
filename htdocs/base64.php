<?php
/**
 * Last commit of this file (GMT):
 * $Id: 849e5f33f21126672c44669bc25309893ac6b5b2 $
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
$title = 'Base64 encode/decode';
file_exists('Include/head.php') && require_once 'Include/head.php';
$source = isSet($_REQUEST['source']) ? trim($_REQUEST['source']) : '';
$srcLen = strlen($source);
$option = $srcLen? '': ' autofocus';
?>
	<p><br/></p>
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
	  <div class="center">
	    <div class="btn-grp">
	      <input type="reset"  value="Reset"  class="btn"/>
	      <input type="submit" value="Encode" class="btn btn-info"    name="encode"/>
	      <input type="submit" value="Decode" class="btn btn-primary" name="decode"/>
	    </div><br/>
	  </div>

	  <div style="float:left">
	    <label>Source string to encode or decode:<br/>
	    <textarea<?php echo $option?> cols="110" rows="15" name="source"><?php echo $source?></textarea>
	    </label><br/>
<?php
if ($srcLen<=0) {
	echo "\t  </div>\n";
} else {
    echo "\t    <p>This string is $srcLen characters long.</p>\n";
    echo "\t  </div>\n";
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
    echo "\t  <div style=\"float:left\">\n";
    echo "\t    <label>$type string is:<br/>\n\n";
    echo "\t    <textarea autofocus cols=\"110\" rows=\"15\" name=\"result\">";
    echo "$result</textarea></label><br style=\"clear:both\"/>\n\n";

    echo "\t    <p>This string is $resLen characters long.</p>\n";
}
?>
	  </div>
	</form>
<?php
file_exists('Include/tail.php') && require_once 'Include/tail.php';

// vim: tabstop=4 shiftwidth=4 expandtab
