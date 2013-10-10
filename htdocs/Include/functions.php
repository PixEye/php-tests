<?php
/**
 * Some functions by Julien Moreau aka PixEye
 * Created on: 2008-08-27
 * Last commit of this file: $Id$
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
 * @param	bool	tell if it should return an HTML &nbsp; before the unit or not
 * @param	uint	the default number of digits wanted
 * @param	string	the base unit string ('B' for Bytes by default)
 * @returns	string	the human readable value of a file size
 */
function bytes2human($size_in_bytes, $html_output=TRUE, $digit=1, $base_unit='B')
{
	$unit = $base_unit;
	$size = $size_in_bytes;
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "K$base_unit"; }
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "M$base_unit"; }
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "G$base_unit"; }
	if ($size>1024) { $size = round($size/1024, $digit); $unit = "T$base_unit"; }
	if ($html_output)
		return sprintf("%1.${digit}f&nbsp;%s", $size, $unit);
	else	return sprintf("%1.${digit}f %s", $size, $unit);
}

/**
 * @author      Julien MOREAU aka PixEye
 * @since       2013-07-23
 */
function getUserAgent()
{
  $ua = trim($_SERVER['HTTP_USER_AGENT']);
  if (''==$ua) return '';

  $MostKnown = array('MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera');
  forEach($MostKnown as $short_name)
  {
    $pos = strIPos($ua, $short_name);
    if (false!==$pos)
    {
      $uav = subStr($ua, $pos); // ignore left part
      $l = strLen($uav);
      if ($l<=0) return $short_name;

      $allowed_chars = '0123456789.,- /';
      $begin = strLen($short_name);
      for($i=$begin+1; $i<$l; $i++)
        if (false===strPos($allowed_chars, $uav[$i]))
        {
          $uav = trim(subStr($uav, 0, $i));
          break;
        }
      return $uav;
    }
  }

  return $ua;
}
# die($_SERVER['HTTP_USER_AGENT'].'<br/>'.PHP_EOL.getUserAgent()); // Test getUserAgent()

/**
 * @author      Julien MOREAU aka PixEye
 * @since       2013-07-23
 */
function getOS()
{
  $ua = trim($_SERVER['HTTP_USER_AGENT']);
  if (''==$ua) return '';

  $MostKnown = array('Windows', 'Linux', 'Mac OS X', 'Unix', 'Android', 'iOS');
  forEach($MostKnown as $short_name)
  {
    $pos = strIPos($ua, $short_name);
    if (false!==$pos)
    {
      $uav = subStr($ua, $pos); // ignore left part
      $l = strLen($uav);
      if ($l<=0) return $short_name;

      $allowed_chars = '0123456789.,- /NTi';
      $begin = strLen($short_name);
      for($i=$begin+1; $i<$l; $i++)
        if (false===strPos($allowed_chars, $uav[$i]))
        {
          $uav = trim(subStr($uav, 0, $i));
          break;
        }
      return $uav;
    }
  }

  return $ua;
}
# die($_SERVER['HTTP_USER_AGENT'].'<br/>'.PHP_EOL.getOS()); // Test getOS()
