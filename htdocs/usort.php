<?php
// Cr�� par Julien Moreau (aka PixEye) le 04/10/2005
// Derni�re version sous CVS (heure GMT) :
// $Header$

header('Content-type: text/plain');

$Elem[0]['nom'] = 'citron';
$Elem[0]['couleur'] = 'jaune';

$Elem[1]['nom'] = 'pomme';
$Elem[1]['couleur'] = 'verte';

$Elem[2]['nom'] = 'raisin';
$Elem[2]['couleur'] = 'violet';

$Elem[3]['nom'] = 'fraise';
$Elem[3]['couleur'] = 'rouge';

echo "Avant :\n";
foreach($Elem as $key => $val)
	echo "$key/ ", $val['nom'], ' (', $val['couleur'], ")\n";

// Crit�re de trie :
$critere = 'couleur';
echo "\nTrie sur le crit�re : '$critere' ...\n\n";

function compare($a, $b)
{
   global $critere;
   return strcmp($a[$critere], $b[$critere]);
}

usort($Elem, 'compare');

echo "Apr�s :\n";
foreach($Elem as $key => $val)
	echo "$key/ ", $val['nom'], ' (', $val['couleur'], ")\n";
?>
