<?php
/**
 * Last commit of this file (GMT):
 * $Id$
 *
 * PHP version 5.2
 *
 * @category PHP
 * @package  PhpTests
 * @author   Julien Moreau <jmoreau@pixeye.net>
 * @license  Affero GPL http://choosealicense.com/licenses/agpl/
 * @version  GIT: $Revision$
 * @link     https://github.com/PixEye/php-tests
 * @since    Local time: $Date$
 * @filesource
 */

/**
 * Created on the 2005-10-24 by julien.moreau78@gmail.com
 * @link http://fr.php.net/manual/fr/types.comparisons.php
 */
$header_file = 'Include/head.php';
if (is_readable($header_file)) {
    include_once $header_file;
}
?>
      <table class="nice center grid">
        <thead>
          <tr>
            <th>\</th>
            <th>is_null()?</th>
            <th>===NULL?</th>
            <th>==NULL?</th>
            <th>empty()?</th>
            <th>isSet()?</th>
            <th>is_scalar()?</th>
            <th>is_string()?</th>
            <th>is_numeric()?</th>
            <th>is_int()?</th>
            <th>is_float()?</th>
            <th>is_array()?</th>
            <th>is_object()?</th>
            <th>getType()</th>
            <th>/</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th>/</th>
            <th>is_null()?</th>
            <th>===NULL?</th>
            <th>==NULL?</th>
            <th>empty()?</th>
            <th>isSet()?</th>
            <th>is_scalar()?</th>
            <th>is_string()?</th>
            <th>is_numeric()?</th>
            <th>is_int()?</th>
            <th>is_float()?</th>
            <th>is_array()?</th>
            <th>is_object()?</th>
            <th>getType()</th>
            <th>\</th>
          </tr>
        </tfoot>
        <tbody>
<?php
$true = 'TRUE';
$false = '<span class="error">FALSE</span>';

$e = new Exception;
$value_list = array(
    'not set', null, '', '0', '10109', '6e7', 0,
    1, -1, 1.2, -3.4, '5e', '8i', '1+1', array(), $e
);

forEach ($value_list as $title) {
    $x = $title;
    if ($title==='not set') {
        unset($x);
    }
    elseIf (is_string($x)) $title = "'$x'";
    elseIf ($x===null) $title = 'NULL';
    elseIf (is_array($x)) $title = 'array()';
    elseIf (is_object($x)) $title = 'object';

    $is_nul = @is_null($x)?$true:$false;
    $seq_nul = @($x===null)?$true:$false;
    $eq_nul = @($x==null)?$true:$false;
    $_empty = @empty($x)?$true:$false;
    $is_set = isSet($x)?$true:$false;
    $is_num = @is_numeric($x)?$true:$false;
    $is_int = @is_int($x)?$true:$false;
    $is_flo = @is_float($x)?$true:$false;
    $is_str = @is_string($x)?$true:$false;
    $is_arr = @is_array($x)?$true:$false;
    $is_obj = @is_object($x)?$true:$false;
    $is_scl = @is_scalar($x)?$true:$false;
    $type = isSet($x)?getType($x):'NULL';

    echo "\t    <tr>",
        "<th>$title</th>",
        "<td>$is_nul</td>",
        "<td>$seq_nul</td>\n\t\t",
        "<td>$eq_nul</td>",
        "<td>$_empty</td>\n\t\t",
        "<td>$is_set</td>",
        "<td>$is_scl</td>\n\t\t",
        "<td>$is_str</td>",
        "<td>$is_num</td>\n\t\t",
        "<td>$is_int</td>",
        "<td>$is_flo</td>\n\t\t",
        "<td>$is_arr</td>",
        "<td>$is_obj</td>\n\t\t",
        "<td>$type</td>",
        "<th>$title</th>",
        "</tr>\n";
}
?>
        </tbody>
      </table>
<?php
$tail_file = 'Include/tail.php';
if (is_readable($tail_file)) {
    include_once $tail_file;
}
