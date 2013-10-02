<?php
/**
 * Create by Julien Moreau (aka PixEye) on 2005-10-04
 *
 * Last commit of this file (GMT):
 * $Id$
 */

if (!headers_sent()) header('Content-type: text/plain');

$Elem[0]['name'] = 'lemon';
$Elem[0]['color'] = 'yellow';

$Elem[1]['name'] = 'apple';
$Elem[1]['color'] = 'green';

$Elem[2]['name'] = 'grape';
$Elem[2]['color'] = 'purple';

$Elem[3]['name'] = 'strawberry';
$Elem[3]['color'] = 'red';

echo "Before:\n";
foreach($Elem as $key => $val)
	echo "$key/ ", $val['name'], ' (', $val['color'], ")\n";

function compare($a, $b)
{
   global $criterion;
   return strCmp($a[$criterion], $b[$criterion]);
}

$criterion = 'color'; // Sort criterion
echo "\n Sort criterion: '$criterion' ...\n\n";
uasort($Elem, 'compare');

echo "After:\n";
foreach($Elem as $key => $val)
	echo "$key/ ", $val['name'], ' (', $val['color'], ")\n";

$criterion = 'name'; // Sort criterion
echo "\n Sort criterion: '$criterion' ...\n\n";
uasort($Elem, 'compare');

echo "After:\n";
foreach($Elem as $key => $val)
	echo "$key/ ", $val['name'], ' (', $val['color'], ")\n";
