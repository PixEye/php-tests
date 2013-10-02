<?php
/**
 * Created on 2005-04-19 by Julien Moreau (aka PixEye)
 */

include_once 'Include/head.php';

$self=basename($_SERVER['PHP_SELF']);

#$host=`hostname -s`;	// ne fonctionne pas en safe mode
$Host=explode('.', $_SERVER['HTTP_HOST']);
$nb_sections=count($Host);
if ($nb_sections==0) $host='';
elseif ($nb_sections==1) $host=$Host[0];
else $host=$Host[$nb_sections-2];

if ($host=='localhost') $host='';
#else $host=ucFirst($host);
?>
    <h2><?php echo $host?></h2>

    <h3>Liste des utilisateurs&nbsp;:</h3>

    <p><?php
	$User=Array();
	// Le principal est ci-dessous :
	foreach(file('/etc/passwd') as $ligne) {
		$Col=explode(':', $ligne);
		echo $Col[0].'&nbsp;('.$Col[2].'), ';
		unset($Col);
	} ?></p>

<?php include_once 'Include/tail.php';
