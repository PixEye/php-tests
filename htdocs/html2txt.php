<?php
$title = "html_entity_decode() with strip_tags() test";
include_once 'Include/head.php';

$html = "&gt;&quot;&laquo;&nbsp;518,52&nbsp;&euro;";
$html.= " &copy;&reg;&Ccedil;&Agrave; &raquo;&quot;&lt;";

echo "\t<p>Initial HTML string: <big>$html</big></p>", PHP_EOL;
echo "\t<p style=\"text-align:left\">Encoded HTML string:", PHP_EOL;
echo "\t  <big>".htmlentities($html)."</big></p>", PHP_EOL;
echo "\t<p>Text string (decoded from the initial HTML string): <big>";
$tmp = html_entity_decode(trim(strip_tags($html)), ENT_QUOTES);
echo htmlSpecialChars($tmp, ENT_NOQUOTES, 'ISO-8859-15');
echo "</big></p>", PHP_EOL;

include_once 'Include/tail.php';
