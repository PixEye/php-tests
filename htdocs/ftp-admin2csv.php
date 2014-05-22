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

$output_sep = ',';
$charset = 'utf-8';
$title = 'Administration FTP';
$output_file = '/tmp/ftp-list.csv';
$keys = array('login', 'password', 'sys_user', 'can_read_logs', 'can_write_site');

is_readable('Include/head.php') && require_once 'Include/head.php';

$all_set = true;
forEach ($keys as $k) {
    $all_set = $all_set && isSet($_POST[$k]);
    $$k = (isSet($_POST[$k]))?$_POST[$k]:'';
}

if ($all_set) {
    $values = array();
    forEach ($keys as $i => $k) {
        $values[$k]= '"'.$$k.'"';
        $keys[$i] = '"'.$k.'"';
    }
    $content = implode($output_sep, $values)."\r\n";

    if (!file_exists($output_file)) { // Add header line:
        $content = implode($output_sep, $keys)."\r\n".$content;
        $mode = 'w';
    } else { // Or append:
        $mode = 'a';
    }

    $fd = fopen($output_file, $mode);
    if (false===$fd) {
        die("Could not open file: '$output_file' for writing!".PHP_EOL);
    }

    $ret = fwrite($fd, $content);
    if (false===$ret) {
        die("Could not write to file: $output_file!".PHP_EOL);
    }

    $is_ok = @fclose($fd);
    if (!$is_ok) {
        syslog(LOG_ERR, __FILE__." fclose() failed for file: '$output_file'!");
    }

    $is_ok = @chmod($output_file, 0600); // chmod to: -rw-------
    if (!$is_ok) {
        syslog(LOG_ERR, __FILE__." chmod() failed for file: '$output_file'!");
    }

    echo "\t<p class=\"center ok\">".
        "All right! Data written in file: <kbd>$output_file</kbd></p>\n";
}

echo "\t<form action=\"", basename(__FILE__), "\" method=\"post\" id=\"form1\">\n";
?>
	  <table class="nice big center grid">
	    <tbody>
	      <tr>
		<th><label for="login">Identifiant&nbsp;:</label></th>
		<td>
		  <div class="tooltip">Entre 2 et 12 chiffres ou lettres</div>
		  <input name="login" id="login" required="required" type="text"
		    class="lowercase" maxlength="12" autofocus="autofocus"
		    pattern="|^[a-z0-9]{2,12}$|" autocomplete="off" value="<?php echo $login?>"/>
		</td>
	      </tr>
	      <tr>
		<th><label for="password">Mot de passe&nbsp;:</label></th>
		<td>
		  <div class="tooltip">Entre 8 et 25 caractères</div>
		  <input name="password" id="password" required="required" type="password"
		    maxlength="25"
		    pattern="|^.{8,25}$|" autocomplete="off" value="<?php echo $password?>"/>
		</td>
	      </tr>
	      <tr>
		<th><label for="sys_user">Utilisateur système&nbsp;:</label></th>
		<td>
		  <select name="sys_user" id="sys_user">
<?php
for ($i=1; $i<=18; $i++) {
    $val = sprintf('admin_sec_%02d', $i);
    $addon = ($val===$sys_user)?' selected="selected"':'';
    printf("\t\t\t<option%s>%s</option>\n", $addon, $val);
}

$checked = ' checked="checked"';
?>
		  </select>
		</td>
	      </tr>
	      <tr>
		<th>Peut lire les logs&nbsp;?</th>
		<td>
		  <label><input name="can_read_logs" required="required" type="radio"
		    value="y"<?php echo ('y'===$can_read_logs)?$checked:''?>/>Oui</label>
		  <label><input name="can_read_logs" required="required" type="radio"
		    value="n"<?php echo ('n'===$can_read_logs)?$checked:''?>/>Non</label>
		</td>
	      </tr>
	      <tr>
		<th>Peut modifier le site&nbsp;?</th>
		<td>
		  <label><input name="can_write_site" required="required" type="radio"
		    value="y"<?php echo ('y'===$can_write_site)?$checked:''?>/>Oui</label>
		  <label><input name="can_write_site" required="required" type="radio"
		    value="n"<?php echo ('n'===$can_write_site)?$checked:''?>/>Non</label>
		</td>
	      </tr>
	      <tr>
		<td colspan="2">
		  <div class="fright"><input type="submit"/></div>
		  <input type="reset"/>
		</td>
	      </tr>
	    </tbody>
	  </table>
	</form>
<?php
is_readable('Include/tail.php') && require_once 'Include/tail.php';
