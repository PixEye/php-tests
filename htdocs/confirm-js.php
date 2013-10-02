<?php
// Last version under CVS (GMT):
// $Header$

@include_once 'error-handler.php';

include_once 'Include/head.php';
$self = basename($_SERVER['PHP_SELF']);

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

switch(substr($lg, 0, 2)) {
	case 'fr':
		$msg = 'Cliquez sur le bouton de votre choix.';
		break;
	default:	$lg = 'en';
		$msg = 'Click on the button of your choice.';
}
?>
<script type="text/javascript">
<!--
	alert(confirm('<?php echo $msg?>'));
-->
</script>
<?php setLocale(LC_ALL, $lg); ?>
	<div style="float:left"><?php echo gmstrFTime('%c')?></div>
<?php include_once 'Include/tail.php'; ?>
