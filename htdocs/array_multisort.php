<?php
/**
 * Last commit of this file (GMT):
 * $Id$
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PhpTests
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 PixEye.net
 * @license   Affero GPL http://choosealicense.com/licenses/agpl/
 * @version   GIT: $Revision$
 * @link      https://github.com/PixEye/php-tests
 * @since     Local time: $Date$
 * @filesource
 */

is_readable('Include/head.php') && require_once 'Include/head.php';

/**
 * Sort a multi-dimensional array.
 *
 * @param array  $a     The array you want to sort
 * @param string $by    The name of the column to sort on
 * @param int    $order SORT_ASC or SORT_DESC
 * @param int    $type  SORT_NUMERIC or SORT_STRING
 *
 * @return array
 * @see array_multisort()
 */
function Sort_Md_array($a, $by, $order, $type)
{
    $sortby = "sort$by"; // This sets up what you are sorting by
    $firstval = current($a); // Pulls over the first array
    $vals = array_keys($firstval); // Grabs the associate Arrays

    forEach ($vals as $init) {
        $keyname = "sort$init";
        $$keyname = array();
    }

    forEach ($a as $key => $row) {
        forEach ($vals as $names) {
            $keyname = "sort$names";
            $test = array();
            $test[$key] = $row[$names];
            $$keyname = array_merge($$keyname, $test);
        }
    }
    array_multisort($$sortby, $order, $type, $a);

    return $a;
}

// Now, to test it
//    Here is an array example:
$test[0]['age'] = 42; $test[0]['name'] = 'David';
$test[1]['age'] = 23; $test[1]['name'] = 'Asma';
$test[2]['age'] = 28; $test[2]['name'] = 'Joseph';

echo "\t<pre class=\"leftBox\">before = "; print_r($test); echo "</pre>\n";
$test = Sort_Md_array($test, 'age', SORT_ASC, SORT_NUMERIC);

echo "<div class=\"leftBox\">";
echo "Call to Sort_Md_array(array, 'age', SORT_ASC, SORT_NUMERIC)</div>\n";
echo "\t<pre class=\"leftBox\">after  = "; print_r($test); echo "</pre>\n";

/* This will return: Array (
    [0] => Array ( [name] => David  [age] => 23 )
    [1] => Array ( [name] => Joseph [age] => 28 )
    [2] => Array ( [name] => Asma   [age] => 42 ) ) */

is_readable('Include/tail.php') && require_once 'Include/tail.php';

// Vim editing preferences (PHP_CodeSniffer compliant):
// vim: tabstop=4 shiftwidth=4 expandtab
