<?php
// Last version under CVS (GMT):
// $Header$

$charset = 'utf-8';
@include_once 'error-handler.php';

$title = 'Test PHP setLocale(), localeConv() &amp; strFTime()';
include_once 'Include/head.php';
$self = basename($_SERVER['PHP_SELF']);

if (!isset($DEBUG)) $DEBUG = 0;

// Important (because of head.php):
if (strlen($lg)==2) {
	if ($lg=='en') $lg = 'en_US';
	else $lg = $lg.'_'.strtoupper($lg).'@euro';
}

// Get languages supported by the system:
$SupportedLg = explode("\n", trim(`locale -a`));

$nb_supported_lg = count($SupportedLg);

// Display supported languages if in debug mode:
echo "  <!-- $nb_supported_lg supported languages",
	" (from 'locales -a')";
if ($DEBUG) { echo " : "; print_r($SupportedLg); }
echo " -->\n";

$DEBUG = TRUE;		// active debug mode

// Language detection from the web user agent preferences:
if (empty($lg)) {
  $lg = 'en_US';	// Default language
  $HTTP_ACCEPT_LANGUAGE = $_SERVER[HTTP_ACCEPT_LANGUAGE];
  if ($DEBUG) echo "<!-- HTTP_ACCEPT_LANGUAGE=\"$HTTP_ACCEPT_LANGUAGE\" -->\n";
  $AcceptLg = explode(',', $HTTP_ACCEPT_LANGUAGE);

  if (is_array($SupportedLg))
    forEach($AcceptLg as $lgt) {
	if ($DEBUG) echo "<!-- lgt = $lgt -->\n";
	$pos = strpos($lgt, ';');
	if ($pos!==FALSE) $lgt = substr($lgt, 0, $pos);
	if ($DEBUG) echo "<!-- lgt = $lgt -->\n";
	$lgt = subStr(trim($lgt), 0, 5);
	if (strlen($lgt)>2 && $lgt[2]=='-') {
		$lgt[2] = '_';
		$lgt[3] = strtoupper($lgt[3]);
		$lgt[4] = strtoupper($lgt[4]);
		#if (!strstr($lgt, '@')) $lgt.='@euro';
	}
	if ($DEBUG) echo "<!-- lgt = $lgt -->\n";
	if (in_Array($lgt, $SupportedLg)) { $lg = $lgt; break; }
    }
  elseif (count($AcceptLg)>0) $lg = $AcceptLg[0];
  if ($DEBUG) echo "  <!-- lg = '$lg' -->\n";
} elseif ($DEBUG) echo "  <!-- lg was already set to : '$lg' -->\n";

$number = -12345.12345;

function displayLocales($lg, $isThePrefered=FALSE) {
	global $number, $charset;

	$tag = 'div';
	if ($isThePrefered) $addon=' class="em"'; else $addon='';
	#$addon = ' class="box"';
	$addon.= ' style="float:left; border:solid 1px"';
?>
        <<?php echo $tag?><?php echo $addon?>>
<?php
	$ret = setLocale(LC_ALL, $lg);
	setLocale(LC_TIME, 'Europe/Paris');
	if ($ret===FALSE) {
		echo "\t\tLanguage '$lg' is not supported by this system.</$tag>\n";
		return;
	}

	$LocaleConfig = localeConv();

	echo "<pre>Language (lg):     <strong>'$lg'</strong>\n\nlocaleConv():\n";
	if (stristr($lg, 'utf')!==FALSE) $cs = 'UTF-8'; else $cs = $charset;
	forEach($LocaleConfig as $key => $val) {
		if (is_array($val)) {
			$$key = $val;
			echo "  $key:\n";
			forEach($val as $key2 => $val2)
				echo "\t$key2:\t\"$val2\"\n";
		} else {
			$val = htmlEntities($val, ENT_NOQUOTES, $cs);
			$$key = $val;
			echo "  $key:   \t\"$val\"\n";
		}
	}

	// Sign specifications:
	if ($number>0) {
		$sign = $positive_sign;
		$sign_posn = $p_sign_posn;
		$sep_by_space = $p_sep_by_space;
		$cs_precedes = $p_cs_precedes;
	} else {
		$sign = $negative_sign;
		$sign_posn = $n_sign_posn;
		$sep_by_space = $n_sep_by_space;
		$cs_precedes = $n_cs_precedes;
	}

	// Number format:
	$n = number_format(abs($number), $frac_digits,
		$decimal_point, $thousands_sep);
	$n = str_replace(' ', '&nbsp;', $n);
	switch($sign_posn) {
		case 0: $n = "($n)"; break;
		case 1: $n = "$sign$n"; break;
		case 2: $n = "$n$sign"; break;
		case 3: $n = "$sign$n"; break;
		case 4: $n = "$n$sign"; break;
		default: $n = "$n [error sign_posn=$sign_posn!]";
	}

	// Currency format:
	if (function_exists('money_format'))
		$mn = money_format('%n', $number);
	$m = number_format(abs($number), $frac_digits,
		$mon_decimal_point, $mon_thousands_sep);
	if ($sep_by_space) $space = ' '; else $space = '';
	if ($cs_precedes) {
		$m = "$currency_symbol$space$m";
		$s = sprintf("%s%s%1.${frac_digits}f",
			$currency_symbol, $space, $number);
	} else {
		$m = "$m$space$currency_symbol";
		$s = sprintf("%1.${frac_digits}f%s%s",
			$number, $space, $currency_symbol);
	}
	$m = str_replace(' ', '&nbsp;', $m);
	$s = str_replace(' ', '&nbsp;', $s);
	switch($sign_posn) {
		case 0: $m = "($m)"; break;
		case 1: $m = "$sign$m"; break;
		case 2: $m = "$m$sign"; break;
		case 3: $m = "$sign$m"; break;
		case 4: $m = "$m$sign"; break;
		default: $m = "$m [error sign_posn=$sign_posn!]";
	}

	echo "\n$n";	// Number
	echo "\n$s  [sprintf(%f)]";		// Monney using sprintf()
	echo "\n$m [number_format()]";	// Monney using number_format()
	if (function_exists('money_format'))
		echo "\n$mn [money_format(%n)]";	// Monney using money_format()
	echo "\n\n".htmlEntities(strFTime('%c'), ENT_NOQUOTES, $cs);
	echo "\n".strFTime('%x');
	echo " | ".strFTime('%X %Z');
	echo "</pre>\n";
	echo '<div class="em">', htmlEntities(strFTime('%A %e %B %Y'), ENT_NOQUOTES, $cs),
		"</div>\n";
  echo "\t</$tag>\n";
}
?>
	<div class="margin">
<?php
displayLocales('en_US.utf8');
displayLocales('en_US');
displayLocales('en_GB');
displayLocales($lg, TRUE);
displayLocales('es_ES@euro');
displayLocales('de_DE@euro');
displayLocales('fr_FR.utf8@euro');
displayLocales('fr_FR.utf8');
displayLocales('pt_PT@euro');
#displayLocales('it_IT@euro');
?>
	  <br style="clear:both" />
	</div>

	<p>What's&nbsp;used:
		<code style='padding-left:1em;'>
			`locale -a`;
			setLocale(LC_ALL,&nbsp;&lt;lg&gt;);
			strFTime('%c');
			strFTime('%x'); | strFTime('%X&nbsp;%Z');
			strFTime('%A&nbsp;%e&nbsp;%B&nbsp;%Y');
		</code></p>

<?php
setLocale(LC_ALL, $lg);
setLocale(LC_TIME, 'Europe/Paris');
?>
  <div style="float:left"><?php echo htmlEntities(gmstrFTime('%c'), ENT_NOQUOTES, $charset)?></div>
<?php include_once 'Include/tail.php'; ?>
