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
 * Convert a byte size into a human readable amount using the units: K, M, G & T
 *
 * @param int the size in bytes
 */
function to_human_size($size_in_bytes, $html_output = TRUE)
{
    if (!is_numeric($size_in_bytes))
        throw new exception("La taille '$size_in_bytes' n'est pas un nombre");

    $size_in_bytes+= 0; // cast string to int
    if (!is_int($size_in_bytes))
        throw new exception("La taille '$size_in_bytes' n'est pas un entier");

    if ($size_in_bytes<1024) return $size_in_bytes;

    /**
     * @see http://fr.php.net/manual/fr/function.number-format.php
     */
    $Units = array('', 'K', 'M', 'G', 'T');
    $unit = floor(log($size_in_bytes, 2) / 10);
    #print('<!-- '.__FUNCTION__."($size_in_bytes) unit='$unit' -->");
    return sprintf('%01.1f%s',
        $size_in_bytes/pow(1024, $unit),
        $Units[$unit]);
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

  $MostKnown = array('Windows', 'Linux', 'Mac OS X', 'Unix', 'Android');
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
