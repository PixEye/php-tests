<?php
$start_time = microtime(true);
error_reporting(-1); ini_set('display_errors', 1); // Report all PHP errors
if (isSet($argv)) return;
# <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
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

@include_once 'Include/my-functions.php';
?>
<html lang="<?php echo $lg?>">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php
	echo $charset?>"<?php echo $sl?>>
    <link rel="Shortcut Icon" type="image/x-icon" href="/favicon.ico"<?php echo $sl?>>
<?php	if (!isset($disable_css)): ?>
    <link rel="stylesheet" type="text/css" href="<?php
	echo $base.$css?>" title="style at load"<?php echo $sl?>>
<?php foreach($CSS as $key => $val) {
	$alt_style = "$css_dir/$val.css";
	if (file_exists($alt_style)) {
		if (is_numeric($key)) $style_title = $val; else $style_title = $key;
		if ($alt_style!=$css) { ?>
    <link rel="alternate stylesheet" type="text/css" href="<?php
	echo $base.$alt_style?>" title="<?php echo $style_title?>"<?php echo $sl?>>
<?php		}
	}
    }
	endif;
    if (isset($head_addon))
      echo '    '.str_replace("\n", "\n    ", trim($head_addon))."\n";
?>
    <title><?php echo strip_tags($title)?></title>
  </head>
  <body>
    <h1><?php echo $title?></h1>
    <div id="main">
	<p class="center" id="source">
<?php
$src = basename($_SERVER['PHP_SELF'].'s');
if (file_exists($src))
	echo "\t  [&nbsp;<a href=\"$src\">Source code of this PHP script</a>&nbsp;]<br/>\n";
echo "\t  PHP version: ", PHP_VERSION;
?></p>

