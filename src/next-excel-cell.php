#!/usr/bin/env php
<?php
assert_options(ASSERT_WARNING, TRUE);
assert_options(ASSERT_ACTIVE, TRUE);
assert_options(ASSERT_BAIL, TRUE); // terminate execution on failed assertions

/**
 * Compute the next Excel column name (useful for the PHPExcel library)
 *
 * The problem in PHP is that: 'A' + 2 = 2
 *
 * @author   PixEye.net
 * @param    string      Base column name
 * @param    int         Optional increment (default is one)
 * @returns  string      The base + increment column name
 * @version  1.1 simplified thanks to @Mark_Baker (see on Twitter)
 * @since    2011-11-18
 *
 * Here is what Mark Baker proposed:
 * https://twitter.com/#!/Mark_Baker/status/137531154898231296
 * return PHPExcel_Cell::stringFromColumnIndex((PHPExcel_Cell::columnIndexFromString($col) + $inc)-1);
 * # it would work - with Excel limits & it gets rid of the costly loop
 */
function nextCol($col, $inc=1)
{
	assert( 'is_string($col)');
	assert('!is_numeric($col)');
	assert( 'is_numeric($inc)');

	for($i=0; $i<$inc; $i++) ++$col;

	assert( 'is_string($col)');
	assert('!is_numeric($col)');
	return($col);
}

// Functional tests:
assert("nextCol('A')=='B'");
assert("nextCol('B')=='C'");
assert("nextCol('A', 2)=='C'");
assert("nextCol('Z')=='AA'");
assert("nextCol('Z', 3)=='AC'");
assert("nextCol('AA')=='AB'");
assert("nextCol('AZ')=='BA'");
assert("nextCol('ZZ')=='AAA'");
assert("nextCol('ZZZ')=='AAAA'");

$cmd = basename(__FILE__, '.php');
echo "$cmd at line ", __LINE__, ': all assertions are true. :)', PHP_EOL;
