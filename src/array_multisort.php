<?php
include_once 'Include/head.php';

function sort_md_array($array, $by, $order, $type) {
	# $array: the array you want to sort
	# $by: the name of the column to sort on
	# $order: SORT_ASC or SORT_DESC
	# $type: SORT_NUMERIC or SORT_STRING

	$sortby = "sort$by"; # This sets up what you are sorting by
	$firstval = current($array); # Pulls over the first array
	$vals = array_keys($firstval); # Grabs the associate Arrays

	foreach ($vals as $init) {
		$keyname = "sort$init";
		$$keyname = array();
	}

	foreach ($array as $key => $row) {
		foreach ($vals as $names) {
			$keyname = "sort$names";
			$test = array();
			$test[$key] = $row[$names];
			$$keyname = array_merge($$keyname, $test);
		}
	}
	array_multisort($$sortby, $order, $type, $array);

	return $array;
}

// Now, to test it
//	Here is an array example:
$test[0]['age'] = 42; $test[0]['name'] = "Dennis";
$test[1]['age'] = 23; $test[1]['name'] = "David";
$test[2]['age'] = 28; $test[2]['name'] = "Joseph";

echo "\t<pre>before = "; print_r($test); echo "</pre>\n";
$test = sort_md_array($test, 'age', SORT_ASC, SORT_NUMERIC);
echo "<div>Call to sort_md_array(array, 'age', SORT_ASC, SORT_NUMERIC)</div>\n";
echo "\t<pre>after  = "; print_r($test); echo "</pre>\n";

# This will return: Array (
#	[0] => Array ( [name] => David  [age] => 23 )
#	[1] => Array ( [name] => Joseph [age] => 28 )
#	[2] => Array ( [name] => Dennis [age] => 42 ) )

include_once 'Include/tail.php';
?>
