<?php
# Created on the 2005-10-24 by julien.moreau78@gmail.com
# see: http://fr.php.net/manual/fr/types.comparisons.php
include_once 'Include/head.php';
?>
      <div align="center">
        <table style="border-collapse:collapse; border:solid 1px">
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
            </tr>
          </thead>
          <tbody>
<?php
$true = 'TRUE';
$false = '<span class="error">FALSE</span>';
$e = new Exception;
forEach(Array('not set', NULL, '', '0', '10109', '6e7', 0, 1, -1, 1.2, -3.4,
		'5e', '8i', '1+1', Array(), $e) as $title) {
        $x = $title;
        if ($title==='not set') unset($x);
        elseif (is_string($x)) $title = "'$x'";
        elseif ($x===NULL) $title = 'NULL';
        elseif (is_array($x)) $title = 'array()';
        elseif (is_object($x)) $title = 'object';

        $is_nul = @is_null($x)?$true:$false;
        $seq_nul = @($x===NULL)?$true:$false;
        $eq_nul = @($x==NULL)?$true:$false;
        $_empty = @empty($x)?$true:$false;
        $is_set = isSet($x)?$true:$false;
        $is_num = @is_numeric($x)?$true:$false;
        $is_int = @is_int($x)?$true:$false;
        $is_flo = @is_float($x)?$true:$false;
        $is_str = @is_string($x)?$true:$false;
        $is_arr = @is_array($x)?$true:$false;
        $is_obj = @is_object($x)?$true:$false;
        $is_scl = @is_scalar($x)?$true:$false;

        echo "\t    <tr style=\"height:25px\">",
                "<th class=\"th2\">$title</th>",
                "<td class=\"cbo\">$is_nul</td>",
                "<td class=\"cbo\">$seq_nul</td>",
                "<td class=\"cbo\">$eq_nul</td>",
                "<td class=\"cbo\">$_empty</td>",
                "<td class=\"cbo\">$is_set</td>",
                "<td class=\"cbo\">$is_scl</td>",
                "<td class=\"cbo\">$is_str</td>",
                "<td class=\"cbo\">$is_num</td>",
                "<td class=\"cbo\">$is_int</td>",
                "<td class=\"cbo\">$is_flo</td>",
                "<td class=\"cbo\">$is_arr</td>",
                "<td class=\"cbo\">$is_obj</td>",
                "</tr>\n";
}
?>
          </tbody>
        </table>
      </div>
<?php include_once 'Include/tail.php'; ?>
