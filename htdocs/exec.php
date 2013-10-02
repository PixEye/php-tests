<?php
$title = "Test de la fonction PHP shell_exec()";
include_once 'Include/head.php';

$lq='&laquo;&nbsp;';
$rq='&nbsp;&raquo;';

$cmd='ls -F';
?>
	<h2 style="margin-left:20px">Commande&nbsp;: <cite><?php echo $lq.$cmd.$rq?></cite></h2>
	<pre style="margin-left:50px"><?php
$output=trim(shell_exec($cmd));
echo str_replace("\n", "\n\t\t", "\n$output");
?></pre>
<?php
include_once 'Include/tail.php';
// vim: tabstop=2 shiftwidth=2 noexpandtab
?>
