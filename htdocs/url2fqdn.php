<?php
include_once 'Include/head.php';
if (!isSet($argv)) echo '<pre>';

function url2fqdn($url) { return parse_url($url, PHP_URL_HOST); }

if (isSet($_SERVER['SCRIPT_URI']))
{
	$url = $_SERVER['SCRIPT_URI'];
	die(PHP_EOL.$url.PHP_EOL.url2fqdn($url).PHP_EOL);
}

// unit tests:
forEach(
	Array(
		'http://www.example.com/',
		'HTTPS://example.com/path?toto=titi&tata=tutu',
	) as $url)
		print(PHP_EOL.htmlSpecialChars($url).PHP_EOL.url2fqdn($url).PHP_EOL);

if(!isSet($argv)) echo '</pre>';
# echo PHP_EOL;
include_once 'Include/tail.php';
