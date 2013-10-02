<?php
include_once 'Include/head.php';

$a1 = array('a' => 'green', 'red', 'blue', 'red');
print('<pre>a1 = ');
var_export($a1);

$a2 = array('b' => 'green', 'yellow', 'red');
print("\na2 = ");
var_export($a2);

$result = array_diff($a1, $a2);
print("</pre>\n<hr/>\n<pre>array_diff(a1, a2)  = a1 - a2 = ");
var_export($result);

$result = array_diff($a2, $a1);
print("</pre>\n<hr/>\n<pre>array_diff(a2, a1) = a2 - a1 = ");
var_export($result);

print("</pre>\n");
include_once 'Include/tail.php';
