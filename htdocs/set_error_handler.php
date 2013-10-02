<?php
// Configure le niveau de rapport d'erreur pour ce script
error_reporting(E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

// Gestionnaire d'erreurs
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	echo "<br />\n<br />\n";
	switch ($errno) {
		case E_USER_ERROR:
			echo "<b>Mon ERREUR</b> [$errno] $errstr<br />\n";
			echo " Erreur fatale à la ligne <b>$errline</b> dans le fichier $errfile";
			# echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")";
			echo "<br />\nAbandon...<br />\n";
			exit(1);
			break;
		case E_USER_WARNING:
			echo "<b>Mon ALERTE</b> [$errno] $errstr<br />\n";
			break;
		case E_USER_NOTICE:
			echo "<b>Ma NOTICE</b> [$errno] $errstr<br />\n";
			break;
		default:
			echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";
			break;
	}
}

// Fonction pour tester la gestion d'erreur
function scale_by_log($vect, $scale)
{
	if (!is_numeric($scale) || $scale <= 0) {
		trigger_error("log(x) for x <= 0 est indéfini, vous utilisez : scale = $scale", E_USER_ERROR);
	}

	if (!is_array($vect)) {
		trigger_error('Entrée incorrect, tableau de valeurs attendu', E_USER_WARNING);
		return null;
	}

	for ($i=0; $i<count($vect); $i++) {
		if (!is_numeric($vect[$i]))
			trigger_error("La valeur à la position $i n'est pas un nombre, utilisation de 0 (zéro)", E_USER_NOTICE);
		$temp[$i] = log($scale) * $vect[$i];
	}
	return $temp;
}

// Configuration du gestionnaire d'erreurs
$old_error_handler = set_error_handler('myErrorHandler');

// Génération de quelques erreurs. Commençons par créer un tableau
echo "vector a\n";
$a = array(2,3, "foo", 5.5, 43.3, 21.11);
print_r($a);

// Générons maintenant un autre tableau, avec des alertes
echo "----\nvector b - a warning (b = log(PI) * a)\n";
$b = scale_by_log($a, M_PI);
print_r($b);

// Ceci est un problème, nous avons utilisé une chaîne au lieu d'un tableau
echo "----\nvector c - an error\n";
$c = scale_by_log("not array", 2.3);
var_dump($c);

// Ceci est une erreur critique : le logarithme de zéro ou d'un nombre négatif est indéfini
echo "----\nvector d - fatal error\n";
$d = scale_by_log($a, -2.5);

# vim: ts=2 sw=2
?>
