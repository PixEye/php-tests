<?php
include_once 'Include/head.php';

$Chars = Array(80 => '¤', 85 => '...', 92 => "'", 96 => '-');
$s = '';
$pat = '/[\x80-\x99]/e';
foreach($Chars as $n => $v) {
	$hex_char = chr(hexdec($n));
	$s.= $hex_char;
	$my_try = preg_replace($pat, '"&#".ord("$0").";"', $hex_char);
	echo "\t<p>Hexa character x$n is:",
		" &#x$n; (should be: \"$v\" which is #", hexdec($n), ")",
		"\n\t my try: ", $my_try, "</p>\n";
}

$s.= "AabéçédéEzZ- °@¤ù!:;,?./ &amp;é\"'(-è_çà)= ¹~#{[|`\^@]} ù*£µ%§";
$s = preg_replace($pat, '"&#".ord("$0").";"', $s);
echo "\t<p>Last example: $s</p>\n";

include_once 'Include/tail.php';
?>
