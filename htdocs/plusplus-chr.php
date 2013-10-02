<?php
// created by jmoreau on 2010-06-29

$charset = 'UTF-8';
# $disable_css = TRUE;
include_once 'Include/head.php';
echo "\t<p>";

$c = 'a';
$d = 'a';
for($i=0; $i<26; $i++) {
	echo $c++;
	echo $d; $d = chr(ord($d)+1);
	echo ' ';
}

echo "</p>\n";
include_once 'Include/tail.php';
?>
