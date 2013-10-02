<?php
/**
 * Some functions by Julien Moreau aka PixEye
 * Created on: 2008-08-27
 */

/**
 * @param	string	the input string
 * @param	uint	the lenght of the returning string
 * @returns	string	the left part of the input string
 */
function left($s, $l) { return substr($s, 0, $l); }

/**
 * @param	string	the input string
 * @param	uint	the lenght of the returning string
 * @returns	string	the right part of the input string
 */
function right($s, $l) { return substr($s, -$l); }

/**
 * @param	uint	the size in bytes
 * @param	string	the base unit string ('B' for Bytes by default)
 * @param	uint	the default number of digits wanted
 * @param	bool	tell if it should return an HTML &nbsp; or not
 * @returns	string	the human readable value of a file size
 */
function human_file_size($size_in_bytes, $base_unit='B', $digit=1, $html_output=TRUE)
{
	$size = $size_in_bytes;
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "K$base_unit"; }
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "M$base_unit"; }
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "G$base_unit"; }
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "T$base_unit"; }
	# setlocale(LC_ALL, 'fr_FR');
	if ($html_output)
		return sprintf("%1.${digit}f&nbsp;%s", $size, $unit);
	else	return sprintf("%1.${digit}f %s", $size, $unit);
}
