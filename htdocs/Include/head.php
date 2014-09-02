<?php
/**
 * Created on the 2005-04-19 by Julien Moreau aka PixEye
 * Last commit of this file: $Id$
 */

$start_time = microtime(true);
error_reporting(-1); ini_set('display_errors', 1); // Report all PHP errors
if (isSet($argv)) return; // Do not make HTML on the CLI
?>
<!doctype html>
<?php

setLocale(LC_TIME, 'Europe/Paris');
if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Paris');

if (!isSet($css_dir)) $css_dir = 'CSS';
$CSS = Array('grey' => 'default', 'Darker' => 'grey', 'MySite-v2' => 'PixEye-v2');
if (!isSet($title)) $title = basename($_SERVER['PHP_SELF']);
if (!isSet($charset)) $charset = 'iso-8859-15';
if (!isSet($css)) $css = $css_dir.'/default.css';
if (!isSet($lg)) $lg = 'fr';
$base = '';
$sl = '/';

@include_once 'Include/functions.php';
?>
<html lang="<?php echo $lg?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php
	echo $charset?>"<?php echo $sl?>>
    <link type="image/x-icon" rel="Shortcut Icon" href="/favicon.ico"<?php echo $sl?>>
<?php	if (!isSet($disable_css)): ?>
    <link type="text/css" rel="stylesheet" href="<?php
	echo $base.$css?>" title="style at load"<?php echo $sl?>>
<?php foreach($CSS as $key => $val) {
	$alt_style = "$css_dir/$val.css";
	if (file_exists($alt_style)) {
		if (is_numeric($key)) $style_title = $val; else $style_title = $key;
		if ($alt_style!=$css) { ?>
    <link type="text/css" rel="alternate stylesheet" href="<?php
	echo $base.$alt_style?>" title="<?php echo $style_title?>"<?php echo $sl?>>
<?php		}
	}
    }
	endif;
    if (isSet($head_addon) && ''!=trim($head_addon))
      echo '    '.str_replace("\n", "\n    ", trim($head_addon))."\n";
?>
    <title><?php echo strip_tags($title)?></title>
  </head>
  <body<?php if (isSet($body_addon)) echo $body_addon?>>
    <h1><?php echo $title?></h1>

    <div id="main">
	<p class="center" id="source">
<?php
$src = basename($_SERVER['PHP_SELF'].'s');
if (file_exists($src))
	echo "\t  [&nbsp;<a href=\"$src\">Source code of this PHP script</a>&nbsp;]<br/>\n";
echo "\t  PHP version: ", PHP_VERSION;
?></p>

