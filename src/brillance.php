<?php
$charset = 'utf-8';
$title = 'Calcul de brillance et de couleur opposée';
include_once 'Include/head.php';
?>
<h2>Quelques exemples&nbsp;:</h2>
<?php
$DEBUG=0;
$Couleur=Array('FF701D' => 'orange', 'E1EF21' => 'jaune',
	'0FA5B3' => 'turquoise', '338DC3' => 'bleu', 'B3C1DD' => 'cyan',
	'A7ABAC' => 'gris', 'FFFFFF' => 'blanc', '000000' => 'noir');

// Retourne la chaine négative de la couleur passée en paramètre :
function negate ($color_code) {
	$ret='';
	$L=strLen($color_code);
	for($i=0; $i<$L; $i++) {
		$origin=base_convert($color_code[$i], 16, 10);
		$newNb=15-$origin;
		$ret.=base_convert($newNb, 10, 16);
	}
	return $ret;
}

// Accepte une couleur du type RGB ou RRGGBB et calcule sa brillance en % :
//  0% pour noir à 100% pour blanc
function brillance ($color_code) {
	global $DEBUG;

	$light=0;
	$L=strLen($color_code);
	if ($L!=3 && $L!=6) return 0;
	$token=$L/3;		// $token is 1 or 2 (nb of char/color)
	if ($DEBUG) echo "<!-- token = $token -->\n";
	$max=pow(16, $token);	// $max is 16 or 256
	if ($DEBUG) echo "<!-- max = $max -->\n";

	$c=0;
	for($i=1; $i<=3; $i++)	// $i is the RGB index
		for($j=$token-1; $j>=0; $j--) {	// $j is 1 or 2
			$char=$color_code[$c++];
			if ($DEBUG) echo "<!-- char = $char -->\n";
			$p=pow(16, $j);
			$addon=$p*base_convert($char, 16, 10);
			if ($DEBUG) echo "<!-- addon = $addon -->\n";
			$light+=$addon;
		}

	if ($DEBUG) echo "<!-- brillance($color_code) = $light -->\n";
	$percent=round($light*100/($max*3));
	if ($DEBUG) echo "<!-- brillance($color_code) = $percent% -->\n";
	return $percent;
}

$SEUIL=50;	// Seuil noir / blanc en % de brillance

forEach($Couleur as $code => $desc) {
	$b=brillance($code);
	if ($b>$SEUIL) $stylo="Black"; else $stylo="White";
	if (50-$b<$SEUIL) $tendance="White"; else $tendance="Black";

	echo "<p class=\"barre\" style=\"color:$stylo; background:#$code;";
	echo " border-color:#$code\">";
	echo "<tt>$code</tt>&nbsp;=&gt; $desc (brillance&nbsp;: $b%)";

	$negCol=strToUpper(negate($code));
	echo "\n  <small style=\"color:$tendance; background:#$negCol\">";
	echo "( couleur invers&eacute;e&nbsp;: <tt>$negCol</tt> )</small>";
	echo "</p>\n\n";
}
# vim: textwidth=80 tabstop=4 shiftwidth=4
include_once 'Include/tail.php';
?>
