<?php
@include 'Include/head.php';
echo '<code>';

$line = ' 31-33 bis rue des 2 Exemples';
$marker = ' ,';

$r = trim(strPBrk(lTrim($line), $marker));
echo "trim(strPBrk(lTrim('$line'), '$marker')) =&gt; ", var_export($r, 1), PHP_EOL;

echo '</code>', PHP_EOL;
@include 'Include/tail.php';
