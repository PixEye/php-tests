<?php
$charset = 'UTF-8';
include_once 'Include/head.php';
$source = isSet($_REQUEST['source']) ? $_REQUEST['source'] : '';
?>
	<p><br/></p>
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
	  <div align="center">
	    <label>Source string to encode:<br/>
				<input type="text" name="source" size="80" value="<?php echo $source?>"/>
	    </label><br/>
	    <br/>
	    <input type="reset" value="Reset"/>
	    <input type="submit" value="Encode" name="encode"/>
	  </div>
	</form>
<?php
if (trim($source)!='') {
	$type = 'Encoded';

	$b64TestCode = 'vJgAudUqJMznKnPdUor+1T8Q5fw='; # =b64(sha1(???))
	$b64TestCode = 'SwbOofLDA88Amta1nAZhQkVzJ0w='; # =b64(sha1(andré))
	$l = strLen($b64TestCode);
	echo "\t<p>Base64 test code: &laquo;&nbsp;<tt>$b64TestCode</tt>&nbsp;&raquo; is $l chars long.</p>\n\n";

	$testCode = base64_decode($b64TestCode);
	$l = strLen($testCode);
	$htmlTestCode = htmlEntities($testCode, ENT_QUOTES, $charset);
	if ($htmlTestCode=='') $htmlTestCode = "base64_decode($b64TestCode)";
	else $htmlTestCode = "&laquo;&nbsp;$htmlTestCode&nbsp;&raquo;";
	echo "\t<p>Test code: $htmlTestCode is $l chars long.</p>\n";

	echo "\n\t<p><br/></p>\n\n";

	// MD5:
	$binCode = md5($source, TRUE);
	$l = strLen($binCode);
	echo "\t<p><strong>MD5 $type</strong> string is: $l characters long.";
	if ($l==16) {
	  if ($binCode==$testCode or $binCode==md5($testCode, TRUE))
	  	echo "<br/>\n\t<span class=\"success\">Match found! :-)</span>";
	  echo "</p>\n";
	  $Tmp = unpack('H*', $binCode); // binary to plain
	  echo "\t<pre>unpack('H*', md5('$source', TRUE)) = "; var_dump($Tmp);
	  echo "</pre>\n";
	} else echo "</p>\n";

	echo "\n\t<p><br/></p>\n\n";

	// SHA1:
	$binCode = sha1($source, TRUE);
	$l = strLen($binCode);
	echo "\t<p><strong>SHA1 $type</strong> string is: $l characters long.";
	if ($l==20) {
	  if ($binCode==$testCode or $binCode==sha1($testCode, TRUE))
	  	echo "<br/>\n\t<span class=\"success\">Match found! :-)</span>";
	  echo "</p>\n";
	  $Tmp = unpack('H*', $binCode); // binary to plain
	  echo "\t<pre>unpack('H*', sha1('$source', TRUE)) = "; var_dump($Tmp);
	  echo "</pre>\n";
	} else echo "</p>\n";

	echo "\n\t<p><br/></p>\n\n";

	// SHA:
	if (function_exists('mhash')) {
		$encoded = '{SHA}'.base64_encode(mHash(MHASH_SHA1, $source));
		$l = strLen($encoded);
		echo "\t<p><strong>SHA $type</strong> string \"$encoded\" is: $l characters long.";
		if ($l==5+28) {
		  if ($encoded==$testCode or $encoded==sha1($testCode, TRUE))
			echo "<br/>\n\t<span class=\"success\">Match found! :-)</span>";
		  echo "</p>\n";
		  $Tmp = base64_encode($encoded); // base64 encode (again)
		  echo "\t<pre>base64_encode('$encoded') = "; var_dump($Tmp);
		  echo "</pre>\n";
		} else echo "</p>\n";
	}

	echo "\n\t<p><br/></p>\n\n";

	$result = crc32($source);
	$l = strLen($result);
	echo "\t<p><strong>CRC32 $type</strong> string is: &laquo;&nbsp;$result&nbsp;&raquo;.<br/><br/>\n".
		"\t This string is $l characters long.</p>\n";
}
include_once 'Include/tail.php';
