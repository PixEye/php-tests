<?php
	//	image.toolbox.inc.php

	function GetSupportedImageTypes() {
		$arrTypes = array();
		$arrTypeBits = array( IMG_GIF => 'GIF', IMG_JPG => 'JPG', IMG_PNG => 'PNG', IMG_WBMP => 'WBMP' );
		foreach ( $arrTypeBits as $intTypeBits => $strType ) {
			if ( imageTypes() & $intTypeBits ) {
				$arrTypes[] = $strType;
			}
		}
		return $arrTypes;
	}

	function gdVersion() {
		if ( ! extension_loaded( 'gd' ) ) {
			return;
		}
		ob_start();
		phpinfo( 8 );
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr( $info, 'gd version' );
		preg_match( '/\d/', $info, $gd );
		return $gd[ 0 ];
	}
?>
