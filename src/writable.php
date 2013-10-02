<?php
$file_path = __FILE__;
$html = !isSet($argv);

if ($html) {
	include_once 'Include/head.php';
	echo '<pre>';
}

$ini_perms = filePerms($file_path);
$str_perms = subStr(sprintf('%o', $ini_perms), -4);
echo "Permissions of '$file_path'='$str_perms'", PHP_EOL;
echo 'is_writable()? ', var_export(is_writable($file_path), true), PHP_EOL;

$tmp_perms = 0400;
$str_perms = subStr(sprintf('%o', $tmp_perms), -4);
$is_ok = @chmod($file_path, $tmp_perms); $class = $is_ok?'ok':'error';
if ($html) echo "<span class=\"$class\">";
echo "chmod(file_path, $str_perms)... ";
if (!$is_ok) echo 'FAILED!';
if ($html) echo '</span> ';

# clearStatCache(); echo 'clearStatCache()', PHP_EOL;
# $str_perms = subStr(sprintf('%o', filePerms($file_path)), -4);
# echo "Permissions of '$file_path'='$str_perms'", PHP_EOL;
echo 'is_writable()? ', var_export(is_writable($file_path), true), PHP_EOL;

$str_perms = subStr(sprintf('%o', $ini_perms), -4);
$is_ok = @chmod($file_path, $ini_perms); $class = $is_ok?'ok':'error';
if ($html) echo "<span class=\"$class\">";
echo "chmod(file_path, $str_perms)... ";
if (!$is_ok) echo 'FAILED!';
if ($html) echo '</span> ';

clearStatCache(); echo 'clearStatCache()', PHP_EOL;
$str_perms = subStr(sprintf('%o', filePerms($file_path)), -4);
echo 'is_writable()? ', var_export(is_writable($file_path), true), PHP_EOL;
echo "Permissions of '$file_path'='$str_perms'", PHP_EOL;
if ($html) echo '</pre>', PHP_EOL;

if ($html) include_once 'Include/tail.php';
