<?php
$charset = 'UTF-8';
include_once 'Include/head.php';

function rappel($buffer)
{
  // remplace toutes les pommes par des carottes
  $ret = str_replace('pommes de terre', 'carottes', $buffer);

  #$ret = str_replace(' by ', ' par ', $ret);
  #$ret = str_replace(' in ', ' dans ', $ret);
  $ret = str_replace(' on line ', ' <span class="success">à la ligne</span> ', $ret);

  return $ret;
}

ob_start('rappel');
?>
	<p>C'est comme comparer des carottes et des pommes de terre.</p>
<?php
	$x = 8 / 0;	// Warning
	echo "\t<p>Et voilà aussi un sac de pommes de terre.</p>\n";
ob_end_flush();

include_once 'Include/tail.php';
?>
