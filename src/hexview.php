<?php
function hexview($data, $columns = 8){
	$dataLength = strlen($data);
	$bytePosition = $columnCount = $lineCount = 0;

	$return = Array();
	$return[] = "\t  ".'<table class="center" border="1" cellspacing="0" cellpadding="2">'."\n";

	$Lines = Array();
	for($n = 0; $n < $dataLength; $n++){
		$Lines[$lineCount][$columnCount++] = substr($data, $n, 1);
		if($columnCount == $columns){
			$lineCount++;
			$columnCount = 0;
		}
	}
	foreach($Lines as $Line){
		$return[] = "\t    <tr>".'<th align="right">'.$bytePosition.": </th>\n\t\t";
		for($n = 0; $n < $columns; $n++){
			if (!isSet($Line[$n])) $Line[$n] = '';
			$return[] = '<td>'.strToUpper(bin2hex($Line[$n])).'</td>';
		}
		$return[] = "\n\t\t<td> &lt;=&gt; </td>\n\t\t";
		for($n = 0; $n < $columns; $n++){
			if (!isSet($Line[$n])) $Line[$n] = '';
			$return[] = '<td>'.htmlEntities($Line[$n]).'</td>';
		}
		$return[] = "</tr>\n";
		$bytePosition = $bytePosition + $columns;
	}
	$return[] = "\t  </table>\n";
	return implode('', $return);
}

include_once 'Include/head.php';

echo "\t<div style=\"text-align:center\">";
for($i=32; $i<128; $i++) echo htmlEntities(chr($i));
echo PHP_EOL, PHP_EOL;

echo hexview(' !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}');
echo "\t</div>", PHP_EOL;

include_once 'Include/tail.php';
