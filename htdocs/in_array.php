<?php
# 2011-01-03 Created by Julien Moreau.

include_once 'Include/head.php';

$V = Array(NULL, '', ' ', '.', 'nogoogle', 'Michel Dupond'); # some values
$F = Array(NULL, '', '.', 'nogoogle'); # forbidden values
?>
	<table class="center nice">
	  <caption>in_array() examples</caption>
	  <thead>
	    <tr>
	      <th>Values:</th>
<?php forEach($V as $v): if(!isSet($v)) $val = 'NULL'; else $val = "'$v'" ?>
				<th><?php echo $val?></th>
<?php endForEach ?>
	    </tr>
	  </thead>
	  <tfoot>
	    <tr>
	      <th>Values:</th>
<?php forEach($V as $v): if(!isSet($v)) $val = 'NULL'; else $val = "'$v'" ?>
				<th><?php echo $val?></th>
<?php endForEach ?>
	    </tr>
	  </tfoot>
	  <tbody>
	    <tr>
	      <th>Value allowed:</th>
<?php forEach($V as $v): if(!isSet($v)) $val = 'NULL'; else $val = "'$v'" ?>
				<td style="text-align:center"><?php echo !in_array($v, $F)?'<b class="ok">TRUE</b>':'FALSE'?></td>
<?php endForEach ?>
	    </tr>
	  </tbody>
	</table>
<?php include_once 'Include/tail.php';
