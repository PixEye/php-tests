<?php
/**
 * Written by Julien Moreau
 */

if (isSet($argv)) die('To be web browsed (not suitable for the CLI)!'.PHP_EOL);

$DEBUG = 1;
$Supported_lg = Array('en', 'fr');
if (isSet($_REQUEST['lg']) && ''!=trim($_REQUEST['lg']))
	$lg = $_REQUEST['lg'];

if ($DEBUG)
	echo '<!-- HTTP_ACCEPT_LANGUAGE="'.$_SERVER['HTTP_ACCEPT_LANGUAGE']."\" -->\n";
$AcceptLg = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

// Language detection:
if (empty($lg)) {
	$lg = 'en_US';		// Default language
	if (is_array($Supported_lg))
		forEach($AcceptLg as $lgt) {
			$lgt = subStr(ltrim($lgt), 0, 2);
			if ($DEBUG) echo "<!-- lgt = $lgt -->\n";
			if (in_Array($lgt, $Supported_lg)) { $lg = $lgt; break; }
		}
	elseif (count($AcceptLg)>0) $lg = $AcceptLg[0];
}

if ($DEBUG) echo "<!-- lg = $lg -->\n";

// Translations:
switch($lg) {
	case 'fr':
		$s = '&nbsp;';
		$title = "Quelle est ma configuration technique&nbsp;?";
		$OS_n_UA = 'Système &amp; navigateur';
		$Lg = "Langues préférées (dans l'ordre)";
		break;
	default: $lg = 'en';
		$s = '';
		$title = 'What is my technical configuration?';
		$OS_n_UA = 'OS &amp; web browser';
		$Lg = 'Prefered languages (in order)';
}

$charset = 'utf-8';
include_once 'Include/head.php';
?>
	<h2>IP<?php echo $s?>:</h2>
	<p><?php echo isSet($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:
	  $_SERVER['REMOTE_ADDR'], ' (&nbsp;'.$_SERVER['REMOTE_ADDR'], '&nbsp;)'?></p>

	<h2><?php echo $OS_n_UA, $s?>:</h2>
	<p><b>HTTP_USER_AGENT:</b> <?php echo $_SERVER['HTTP_USER_AGENT']?></p>
	<p><b>Browser:</b> <?php echo getUserAgent()?></p>
	<p><b>OS:</b> <?php echo getOS()?></p>

	<h2><?php echo $Lg, $s?>:</h2>
    <ol>
<?php
forEach($AcceptLg as $l) {
	$l2 = substr($l, 0, 2);
	$f = "../img/flag-$l2.gif";
	if (!file_exists($f)) $i = '';
	else $i = "<img alt=\"$l2 flag\" src=\"$f\"/> ";
	echo "\t<li>$i$l</li>\n";
}
?>
    </ol>
<?php include_once 'Include/tail.php';
