<?php
// created by jmoreau on 2011-02-25

$charset = 'UTF-8';
include_once 'Include/head.php';
echo "\t<pre>";

$A = Array(0, 1, 2);
echo 'A = '.var_export($A, 1).";\n\n";

list($a, $b) = $A;
echo 'list($a, $b) = $A'.";\n";
echo '$a = '.var_export($a, 1).'; ';
echo '$b = '.var_export($b, 1).";\n\n";

list($c, $d, $e, $f) = $A;
echo 'list($c, $d, $e, $f) = $A'.";\n";
echo '$c = '.var_export($c, 1).'; ';
echo '$d = '.var_export($d, 1).'; ';
echo '$e = '.var_export($e, 1).'; ';
echo '$f = '.var_export($f, 1).';';
echo "</pre>\n";
include_once 'Include/tail.php';
?>
