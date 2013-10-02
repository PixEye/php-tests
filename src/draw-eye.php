<?php
if (!isset($x) or !is_numeric($x) or $x<=0) $x = 640;
if (!isset($y) or !is_numeric($y) or $y<=0) $y = 480;

if (!function_exists('imageCreateTrueColor')) die("GD lib not available!\n");

$img = imageCreateTrueColor($x, $y);
if (function_exists('imageAntiAlias')) @imageAntiAlias($img, true);

function imageSmoothCircle( &$img, $cx, $cy, $cr, $color ) {
       $ir = $cr;
       $ix = 0;
       $iy = $ir;
       $ig = 2 * $ir - 3;
       $idgr = -6;
       $idgd = 4 * $ir - 10;
       $fill = imageColorExactAlpha( $img, $color[ 'R' ], $color[ 'G' ], $color[ 'B' ], 0 );
       imageLine( $img, $cx + $cr - 1, $cy, $cx, $cy, $fill );
       imageLine( $img, $cx - $cr + 1, $cy, $cx - 1, $cy, $fill );
       imageLine( $img, $cx, $cy + $cr - 1, $cx, $cy + 1, $fill );
       imageLine( $img, $cx, $cy - $cr + 1, $cx, $cy - 1, $fill );
       $draw = imageColorExactAlpha( $img, $color[ 'R' ], $color[ 'G' ], $color[ 'B' ], 42 );
       imageSetPixel( $img, $cx + $cr, $cy, $draw );
       imageSetPixel( $img, $cx - $cr, $cy, $draw );
       imageSetPixel( $img, $cx, $cy + $cr, $draw );
       imageSetPixel( $img, $cx, $cy - $cr, $draw );
       while ( $ix <= $iy - 2 ) {
	   if ( $ig < 0 ) {
	       $ig += $idgd;
	       $idgd -= 8;
	       $iy--;
	   } else {
	       $ig += $idgr;
	       $idgd -= 4;
	   }
	   $idgr -= 4;
	   $ix++;
	   imageLine( $img, $cx + $ix, $cy + $iy - 1, $cx + $ix, $cy + $ix, $fill );
	   imageLine( $img, $cx + $ix, $cy - $iy + 1, $cx + $ix, $cy - $ix, $fill );
	   imageLine( $img, $cx - $ix, $cy + $iy - 1, $cx - $ix, $cy + $ix, $fill );
	   imageLine( $img, $cx - $ix, $cy - $iy + 1, $cx - $ix, $cy - $ix, $fill );
	   imageLine( $img, $cx + $iy - 1, $cy + $ix, $cx + $ix, $cy + $ix, $fill );
	   imageLine( $img, $cx + $iy - 1, $cy - $ix, $cx + $ix, $cy - $ix, $fill );
	   imageLine( $img, $cx - $iy + 1, $cy + $ix, $cx - $ix, $cy + $ix, $fill );
	   imageLine( $img, $cx - $iy + 1, $cy - $ix, $cx - $ix, $cy - $ix, $fill );
	   $filled = 0;
	   for ( $xx = $ix - 0.45; $xx < $ix + 0.5; $xx += 0.2 ) {
	       for ( $yy = $iy - 0.45; $yy < $iy + 0.5; $yy += 0.2 ) {
		   if ( sqrt( pow( $xx, 2 ) + pow( $yy, 2 ) ) < $cr ) $filled += 4;
	       }
	   }
	   $draw = imageColorExactAlpha( $img, $color[ 'R' ], $color[ 'G' ], $color[ 'B' ], ( 100 - $filled ) );
	   imageSetPixel( $img, $cx + $ix, $cy + $iy, $draw );
	   imageSetPixel( $img, $cx + $ix, $cy - $iy, $draw );
	   imageSetPixel( $img, $cx - $ix, $cy + $iy, $draw );
	   imageSetPixel( $img, $cx - $ix, $cy - $iy, $draw );
	   imageSetPixel( $img, $cx + $iy, $cy + $ix, $draw );
	   imageSetPixel( $img, $cx + $iy, $cy - $ix, $draw );
	   imageSetPixel( $img, $cx - $iy, $cy + $ix, $draw );
	   imageSetPixel( $img, $cx - $iy, $cy - $ix, $draw );
       }
}

// Définition de quelques couleurs :
$black  = imageColorAllocate($img,   0,   0,   0);
$transp = imageColorAllocate($img, 220, 220, 220);
$white  = imageColorAllocate($img, 255, 255, 255);

// Transparence :
imageColorTransparent($img, $transp);
imageFill($img, 0, 0, $transp);

// Calculs :
$x2 = round($x/2);
$y2 = round($y/2);
$x3 = round($x*.6);
$y3 = round($y*.6);
$r  = round(min($x2, $y2)*.9);
$r2 = round($r/2);

// On dessine la (ou les) ellipse(s) :
#imageEllipse($img, $x2, $y2, $x2, $y2, $white);
 imageSmoothCircle($img, $x2, $y2, $r, array('R' => 0xFF, 'G' => 0xCC, 'B' => 0x00));
imageFilledEllipse($img, $x2, $y2, $x3, $y3, $white);
 imageSmoothCircle($img, $x2, $y2, $r2, $black);

if (!headers_sent()) {
	header('Content-type: image/png');
	imagePNG($img);
}
?>
