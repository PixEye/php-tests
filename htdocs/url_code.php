<?php
$charset = 'UTF-8';
include_once 'Include/head.php';
$source = isSet($_REQUEST['source']) ? $_REQUEST['source'] : '';
?>
	<div align="center">
	<p><br/></p>
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
	    <label>Source string to encode or decode:<br/>
				<textarea cols="100" rows="15" name="source"><?php echo $source?></textarea>
	    </label><br/><br/>
	    <input type="reset" value="Reset"/>
	    <input type="submit" value="Encode" name="encode"/>
	    <input type="submit" value="Decode" name="decode"/>
	</form>
<?php
if (trim($source)!='') {
	#echo "<pre>_REQUEST = "; print_r($_REQUEST); echo "</pre>\n";
	if (isSet($_REQUEST['encode'])) {
	  $type = 'Encoded';
	  $result = urlEncode($source);
	} else {
	  $type = 'Decoded';
	  $result = urlDecode($source);
	  $result = str_replace('<br>', "<br/>\n", $result);
	  $result = htmlSpecialChars($result);
	}
	$l = strlen($result); # $result = nl2br($result);
	echo "\t<p><b>$type</b> string is:</p>\n\n";
	echo "\t<textarea cols=\"100\" rows=\"15\" name=\"result\">$result</textarea>\n\n";
	echo "\t<p>This string is $l characters long.";
	if ($l==16) {
	  if ($result==md5('toto', TRUE)) echo "<br/>\n\t Match found! :)";
	  echo "</p>\n";
	  $Tmp = unpack('H*', $result);
	  echo "<pre>unpack('H*', $result) = "; var_dump($Tmp); echo "</pre>\n";
	} else echo "</p>\n";
}
?>
	</div>
<?php include_once 'Include/tail.php';
