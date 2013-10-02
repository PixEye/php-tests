<?php
$charset = 'UTF-8';
$disable_css = TRUE;
include_once 'Include/head.php';
$nbCmdToday = 1;
for($i=8; $i<=36; $i++)
	printf("\t<div style=\"font-size:%dpx\">%dpx/ Aimez-vous ce bon vieux whiskey&nbsp;?</div>\n", $i, $i);
echo "\t<hr/>\n";
include_once 'Include/tail.php';
?>
