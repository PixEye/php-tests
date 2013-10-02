<?php
include_once 'Include/head.php';

/**
 * @see http://fr.php.net/manual/fr/language.exceptions.php
 * @see http://www.php.net/~helly/php/ext/spl/classException.html
 */
$Classes = Array('Exception', 'ErrorException',
	// SPL = Standard PHP Library:
	'LogicException', 'BadFunctionCallException', 'BadMethodCallException',
	'DomainException', 'InvalidArgumentException', 'LengthException',
	'OutOfRangeException', 'RuntimeException', 'OutOfBoundsException',
	'OverflowException', 'RangeException', 'UnderflowException',
	// Others:
	'AuthenticationException', 'AuthException');
forEach($Classes as $classe) {
	echo "\t<p>";
	if (class_exists($classe))
		echo "<span class=\"success\">The class \"<b>$classe</b>\" exists.</span>";
	else
		echo "<span class=\"error\">The class \"$classe\" does not exist.</span>";
	echo "</p>\n";
}

include_once 'Include/tail.php';
?>
