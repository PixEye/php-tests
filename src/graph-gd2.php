<?php
// Créé le 12/12/2006 par jmoreau@fr.st

// Traitement des paramètres :
$DefaultParams = Array('w' => 800, 'h' => 600, 'f' => '200*cos($x/50)');

foreach($DefaultParams as $key => $def_val) {
	if (isset($_REQUEST[$key])) $$key = $_REQUEST[$key];
	else $$key = $def_val;
	if ($key!='f' && (!is_numeric($$key) or $$key<=0 or $$key>2000))
/*	if ($key=='f') $$key = quoteMeta($$key);
	elseif (!is_numeric($$key) or $$key<=0 or $$key>2000)	*/
		$$key = $def_val;
}
if (!isset($w) or !is_numeric($w) or $w<=0) $w = 640;
if (!isset($h) or !is_numeric($h) or $h<=0) $h = 480;

// Pour avoir des lignes anti-aliasées :
require_once('Include/image.toolbox.inc.php');
require_once('Include/image.antialias.inc.php');

$Color = Array(
	'black'  => Array(0x00, 0x00, 0x00),
	'blue'   => Array(0x00, 0x00, 0xAA),
	'green'  => Array(0x00, 0xAA, 0x00),
	'grey'   => Array(0xCC, 0xCC, 0xCC),
	'red'    => Array(0xAA, 0x00, 0x00),
	'transp' => Array( 220,  220,  220),
	'white'  => Array(0xFF, 0xFF, 0xFF),
	'yellow' => Array(0xF0, 0xF0, 0x00)
);

if ($gdv=gdVersion()) {
	if ($gdv>=2) {
		$img = imageCreateTrueColor($w, $h);
		$tmode = true;
		// Définition de quelques couleurs :
		foreach($Color as $col => $rgb) {
			list($r, $g, $b) = $rgb;
			$$col = imageColorExactAlpha($img, $r, $g, $b, 0);
		}
	} else {
		$img = imageCreate($w, $h);
		$tmode = false;
		// Définition de quelques couleurs :
		foreach($Color as $col => $rgb) {
			list($r, $g, $b) = $rgb;
			$$col = imageColorAllocate($img, $r, $g, $b, 0);
		}
	}
} else	die("The GD extension is not available.\n");

if (function_exists('imageAntiAlias')) imageAntiAlias($img, true);

// Définition du style (point par point) pour les lignes de la grille :
imageSetStyle($img, Array($grey, $white, $white, $white));

// Attribution des couleurs :
$axe_col    = $black;
$func_col   = $blue;
$grid_col   = IMG_COLOR_STYLED;
$pos_col    = $yellow;
$neg_col    = $red;
$shadow_col = $black;
$units_col  = $white;

// Transparence :
imageFill($img, 0, 0, $transp);
#imageColorTransparent($img, $transp);	// [dés]active ici

// Calculs :
$x2 = round($w/2);	// x du centre
$y2 = round($h/2);	// y du centre
$m  = min($x2, $y2);	// minimum entre largeur et hauteur de l'image
$d  = round($m*.1);	// delta de marge
$nx = 20;		// nb lignes horizontales dans la grille
$ny = 10;		// nb lignes  verticales  dans la grille

// On dessine le (ou les) rectangle(s) :
imageFilledRectangle($img, $d, $d, $w-$d, $h-$d, $white) or exit();

// On dessine les lignes (verticales et horizontales) :
for($y=$h-$d; $y>=$d; $y-=round(($h-$d-$d)/$ny))
	imageLine($img, $d, $y, $w-$d, $y, $grid_col) or exit(); // Ligne horizontale

for($x=$w-$d; $x>=$d; $x-=round(($w-$d-$d)/$nx))
	imageLine($img, $x, $d, $x, $h-$d, $grid_col) or exit(); // Ligne verticale

// Le texte à dessiner
$text = "(GD$gdv test)    f(x) = $f";
$x = $x2; $y = round($d*.7);
$font_size = 12;

// Choix de la police de caractères (font) :
#$font = 'arial.ttf';
$font = '/usr/share/fonts/truetype/freefont/FreeSans.ttf'; // ok for debian

// Set the text centered:
### Get exact dimensions of text string
$box = @imageTTFBbox($font_size, 0, $font, $text);
if ($box===FALSE) {
	$font = 'fonts/FreeSans.ttf';
	$box = @imageTTFBbox($font_size, 0, $font, $text);
}

if ($box!==FALSE) {
  ### Get width of text from dimensions
  $text_width = abs($box[4] - $box[0]);

  ### Get height of text from dimensions
  # $text_height = abs($box[5] - $box[1]);

  ### Get x-coordinate of centered text horizontally using length of the image and length of the text
  $x = ($w/2)-($text_width/2)-2;

  ### Get y-coordinate of centered text vertically using height of the image and height of the text
  #$y = ($h/2)+($text_height/2);

  // On pose d'abord l'ombre avant le titre :
  #imageTTFText($img, $font_size, 0, $x+1, $y+1, $grey, $font, $text) or exit();

  // Ajout du texte
  imageTTFText($img, $font_size, 0, $x, $y, $func_col, $font, $text) or exit();
} else imageString($img, 5, ($w-300)/2, 5, $text, $func_col);

// Une petite fonction cosinus pour faire joli... :
for($x=0; $x<=$w-$d-$d; $x++) {
	$rx = $x+$d;
	$syntax_ok = eval("\$y = $f;");
	if ($syntax_ok===FALSE) die("Syntax error here: \$y = $f\n");
	$ry = $y2-$y;

	if ($ry<$d) { $ry = $d; $out = 1; }
	elseif ($ry>$h-$d) { $ry = $h-$d; $out = 1; }
	else $out = 0;

	// 1ère méthode : point par point (simple)...
	#imageSetPixel($img, $rx, $ry, $func_col);

	// 2ième méthode : points reliés...
	#if (isset($old_x) && isset($old_y))
	if (!$out && isset($old_x))	// optimized
		#imageLine($img, $old_x, $old_y, $rx, $ry, $func_col);
		imageSmoothLine($img, $old_x, $old_y, $rx, $ry, $func_col, $tmode);

	// Remplissage :
	if ($y<0) $color = $neg_col; else $color = $pos_col;
	imageLine($img, $rx, $y2, $rx, $ry, $color);

	$old_x = $rx; $old_y = $ry;
}

// Unités sur les axes :
$font_size = 8;
$xt = 5;
for($y=$h-$d; $y>=$d; $y-=round(($h-$d-$d)/$ny)) {
    if ($box!==FALSE) {
	imageTTFText($img, $font_size, 0, $xt+1, $y+6, $shadow_col, $font, $y2-$y) or exit();
	imageTTFText($img, $font_size, 0, $xt  , $y+5, $units_col,  $font, $y2-$y) or exit();
    } else {
	imageString($img, 2, $xt+1, $y-9, $y2-$y, $shadow_col);
	imageString($img, 2, $xt,   $y-8, $y2-$y, $units_col);
    }
}
$yt = $y2+$font_size+3;
for($x=$w-$d; $x>=$d; $x-=round(($w-$d-$d)/$nx)) {
    if ($box!==FALSE) {
	imageTTFText($img, $font_size, 0, $x-$font_size+1, $yt+1, $shadow_col, $font, $x-$d) or exit();
	imageTTFText($img, $font_size, 0, $x-$font_size  , $yt  , $units_col , $font, $x-$d) or exit();
    } else {
	imageString($img, 2, $x-$font_size+1, $yt-9, $x-$d, $shadow_col);
	imageString($img, 2, $x-$font_size,   $yt-8, $x-$d, $units_col);
    }
}

if ($box===FALSE) imageString($img, 3, 5, $h-20, 'No true type font found on this server. :-(', $axe_col); # )

// L'axe des x :
imageLine($img, $d, $y2, $w-$d, $y2, $axe_col) or exit();
// L'axe des y :
imageLine($img, $d, $d, $d, $h-$d, $axe_col) or exit();

// Affichage de l'image :
if (!headers_sent()) {
	header('Content-type: image/png');
	imagePNG($img);
}
?>
