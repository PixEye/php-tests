<?php
	//	image.antialias.inc.php

	function imageSmoothLine( $image, $x1, $y1, $x2, $y2, $color, $trueColor = true ) {
		$fullColor = imageColorsForIndex( $image, $color );
		if ( abs( $x1 - $x2 ) > abs( $y1 - $y2 ) ) {
			$m = ( $y2 - $y1 ) / ( $x2 - $x1 );
			$b = $y1 - $m * $x1;
			$strx = min( $x1, $x2 );
			$endx = max( $x1, $x2 );
			for ( $x = $strx; $x <= $endx; $x++ ) {
				imageSetSmoothPixel( $image, $x, $m * $x + $b, $fullColor, $trueColor );
			}
		} else {
			$m = ( $x2 - $x1 ) / ( $y2 - $y1 );
			$b = $x1 - $m * $y1;
			$stry = min( $y1, $y2 );
			$endy = max( $y1, $y2 );
			for ( $y = $stry; $y <= $endy; $y++ ) {
				imageSetSmoothPixel( $image, $m * $y + $b, $y, $fullColor, $trueColor );
			}
		}
	}

	//	Draw an antialiased pixel. This function basically allows x and y to be set as floats.
	function imageSetSmoothPixel( $image, $x, $y, $fullColor, $trueColor = true ) {
		$fx = floor( $x );
		$fy = floor( $y );
		$cx = ceil( $x );
		$cy = ceil( $y );
		$xa = $x - $fx;
		$xb = $cx - $x;
		$ya = $y - $fy;
		$yb = $cy - $y;
		if ( $cx == $fx and $cy == $fy ) {
			imageSetSubPixel( $image, $fx, $fy, 0.0, 1.0, $fullColor, $trueColor );
		} else {
			imageSetSubPixel( $image, $fx, $fy, $xa + $ya, $xb + $yb, $fullColor, $trueColor );
			if ( $cy != $fy ) {
				imageSetSubPixel( $image, $fx, $cy, $xa + $yb, $xb + $ya, $fullColor, $trueColor );
			}
			if ( $cx != $fx ) {
				imageSetSubPixel( $image, $cx, $fy, $xb + $ya, $xa + $yb, $fullColor, $trueColor );
				if ( $cy != $fy ) {
					imageSetSubPixel( $image, $cx, $cy, $xb + $yb, $xa + $ya, $fullColor, $trueColor );
				}
			}
		}
	}

	function imageSetSubPixel( $image, $x, $y, $a, $b, $fullColor, $trueColor = true ) {
		$tempColor = imageColorsForIndex( $image, imageColorAt( $image, $x, $y ) );
		$tempColor[ 'red' ]   = round( $tempColor[ 'red' ]   * $a + $fullColor[ 'red' ]   * $b );
		$tempColor[ 'green' ] = round( $tempColor[ 'green' ] * $a + $fullColor[ 'green' ] * $b );
		$tempColor[ 'blue' ]  = round( $tempColor[ 'blue' ]  * $a + $fullColor[ 'blue' ]  * $b );
		if ( $tempColor[ 'red' ]   > 255 ) $tempColor[ 'red' ]   = 255;
		if ( $tempColor[ 'green' ] > 255 ) $tempColor[ 'green' ] = 255;
		if ( $tempColor[ 'blue' ]  > 255 ) $tempColor[ 'blue' ]  = 255;
		if ( $trueColor ) {
			$newColor = imageColorExactAlpha( $image, $tempColor[ 'red' ], $tempColor[ 'green' ], $tempColor[ 'blue' ], 0 );
		} else {
			if ( imageColorExact( $image, $tempColor[ 'red' ], $tempColor[ 'green' ], $tempColor[ 'blue' ] ) == -1 ) {
				imageColorAllocate( $image, $tempColor[ 'red' ], $tempColor[ 'green' ], $tempColor[ 'blue' ] );
			}
			$newColor = imageColorExact( $image, $tempColor[ 'red' ], $tempColor[ 'green' ], $tempColor[ 'blue' ] );
		}
		imageSetPixel( $image, $x, $y, $newColor );
	}
?>
