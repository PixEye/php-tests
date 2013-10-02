<?php
@include 'Include/head.php';
echo '<code>';

$line = ' 31-33 bis rue des 2 Exemples ';

$r = preg_replace('|\D+|', '', $line);
echo "preg_replace('|\D+|', '', '$line') =&gt; ", var_export($r, 1);
echo ' <span class="error"># NOK!</span>', PHP_EOL;

echo '<br/><br/>', PHP_EOL;
$pattern = '|^(\d+[-\d]*).*$|';
$r = preg_replace($pattern, '\1', lTrim($line));
echo "preg_replace('$pattern', '\\1', lTrim('$line')) =&gt; ", var_export($r, 1);
echo ' <span class="warn"># OK with lTrim()</span>', PHP_EOL;

echo '<br/><br/>', PHP_EOL;
$pattern = '|\D*(\d+[-\d]*).*|';
$r = preg_replace($pattern, '\1', $line);
echo "preg_replace('$pattern', '\\1', '$line') =&gt; ", var_export($r, 1);
echo ' <span class="ok"># OK without any trim()</span>', PHP_EOL;

echo '</code>', PHP_EOL;
@include 'Include/tail.php';
