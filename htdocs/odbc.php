<?php
/**
 * @see: http://www.php.net/manual/en/function.odbc-connect.php
 * @author Julien Moreau (aka PixEye)
 * @since 2009-11-05
 */

$charset = 'UTF-8';
include_once 'Include/head.php';

// Basic configuration:
$driver = 'MySQL ODBC 3.51 Driver';
$db_user = 'root';
$db_name = 'my_dbname';
$odbc_source = 'my_odbc_source';

// My server:
$db_host = 'localhost';
$db_pass = 'FAKE';
@include_once 'Include/odbc-config.php';	// <-- your own config here

#$dsn = 'Driver={'."$driver}; Server=$db_host; Database=$db_name";
$dsn = 'DRIVER={'."$driver}".
	"; Server=$db_host".
	"; CommLinks=tcpip(Host=$db_host)".
	"; DatabaseName=$db_name".
	"; uid=$db_user; pwd=";

$fake_pw = str_repeat('*', 8);
echo "\t<div>user = '<strong>$db_user</strong>'</div>\n";
echo "\t<div>odbc_source = '<strong>$odbc_source</strong>'</div>\n";
echo "\t<div>dsn = '<strong>$dsn$fake_pw</strong>'</div>\n";

$dsn.= $db_pass;

echo "\t<pre class=\"error\" style=\"text-align:left\">";
if (!function_exists('odbc_connect'))
	die('Undefined function odbc_connect()!</pre></div></body></html>'.PHP_EOL);

$Connection = odbc_connect($dsn, $db_user, $db_pass);

if (!$Connection) echo "</pre>\n";
else {
	echo "</pre>\n";

	echo "\t<p class=\"success\">Connected.</p>\n";

	$sql_req = 'select @@identity';
#	$sql_req = 'select Name from customer';
#	$sql_req = 'select @@Name';
#	$sql_req = 'select @@name';
	echo "\t<p>Requête SQL&nbsp;: <tt>$sql_req</tt>.</p>\n";

	echo "\t<p class=\"error\">";
	try {
		$result_handle = odbc_exec($Connection, $sql_req);	// pb here
#		$result_handle = odbc_tables($Connection);		// pb here (as well)
	} catch (Exception $E) {
		echo "ERREUR&nbsp;!\n";
	}

	if (isSet($result_handle) && $result_handle) {
		$Result = odbc_result($result_handle, 1);
		// make here all what you want with the Result

		odbc_free_result($result_handle);
	}

	odbc_close($Connection);
	echo "&nbsp;</p>\n";

	if (isSet($Result)) {
		$type = getType($Result);
		echo "\t<p>Result is a(n) '$type':</p>\n";

		echo "\t<pre>";
		var_dump($Result);
		echo "</pre>\n";
	}
}

include_once 'Include/tail.php';
