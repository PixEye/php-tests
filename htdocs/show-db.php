<?php
/**
 * Created on 2005-05-23 by Julien Moreau (aka PixEye)
 *
 * Last commit of this file (GMT):
 * @version $Id$
 */

$user = 'root';
$host = '127.0.0.1';
if (isset($_REQUEST['db'])) $db = $_REQUEST['db']; else $db = '';

// Number of lines to display:
$nb_lignes = isset($_REQUEST['nb_lignes']) ? $_REQUEST['nb_lignes'] : 2;

// -----------------------------------------------------------------------------

$self = basename($_SERVER['PHP_SELF']);
$title = $host;
if (trim($db)!='') $title = "$db @&nbsp;$title";

include_once 'Include/head.php';	// head HTML

// To display the date in the right language with strFTime():
if (trim($lg)=='') $lg = 'en';
$LG = $lg.'_'.strToUpper($lg);
if ($lg=='fr' or $lg=='es') $LG.='@euro';
echo "\t<!-- lg : '$lg' => LG : '$LG' -->\n\n";
setLocale(LC_ALL, $LG);

// Extraction et affichage du schéma de la base :
echo '<pre class="error left">';
$connect = mysql_connect($host, $user, 'gimli');
echo '</pre>', PHP_EOL;
if (!$connect) die(mysql_error().'</div></body></html>'.PHP_EOL);

if (trim($db)=='') {
	$res1 = mysql_query('SHOW DATABASES');
	if (!$res1) die(mysql_error());
} else {
	$ret = mysql_select_db($db);
	if (!$ret) die(mysql_error());

	$res1 = mysql_query('SHOW TABLES');
	if (!$res1) die(mysql_error());
}

$nb_blocks = mysql_num_rows($res1);
echo "\t<!-- Nb blocks to display: $nb_blocks -->\n";

if ($nb_blocks<5) $nb_lignes = 1;
echo "\t<!-- Nb lines to display: $nb_lignes -->\n";

$nb_tables_par_ligne = ceil($nb_blocks / $nb_lignes);
echo "\t<!-- => nb cols: $nb_tables_par_ligne -->\n\n";

$n = 0;
while($Row = mysql_fetch_row($res1)) {
	if (trim($db)=='') {
		$bado = $Row[0];
		echo "\t\t<p>Base ".++$n." &nbsp;: <a href=\"$_SERVER[PHP_SELF]?db=$bado\">$bado</a></p>\n";
	} else {
		$table = $Row[0];
?>
		<span class="leftBox">
			<?php echo ++$n.'.&nbsp;<b>'.$table.'</b>'?><br />
<?php
		$res2 = mysql_query("DESC $table");
		if (!$res2) die(mysql_error());

		while($Row = mysql_fetch_assoc($res2)) {
			$addon = '';
			$info_bulle = $Row['Type'];
			    if ($Row['Key']=='PRI')  $addon.='&nbsp;PK';
			elseif ($Row['Key']=='MUL')  $addon.='&nbsp;FK';

			if ($Row['Default']==NULL) {
				$addon.='&nbsp;N';
				$info_bulle.=' default: NULL';
			} else	$info_bulle.=' default: '.$Row['Default'];

			if ($Row['Extra']=='auto_increment') $addon.='&nbsp;ai';
			if ($addon!='') $addon="<sup>$addon</sup>";

			echo "\t\t\t<small title=\"$info_bulle\" style=\"cursor:help\"",
				">$Row[Field]$addon</small><br />\n";
		}
?>
		</span>
<?php		#if (($n%$nb_tables_par_ligne)==0) echo "\t\t<br style=\"clear:both\" />\n";
	}
}
?>

<!--	<br style=\"clear:both" />
	<div class="margin" style="float:left"><?php echo strFTime('%c')?>	-->
<?php include_once 'Include/tail.php'; ?>
