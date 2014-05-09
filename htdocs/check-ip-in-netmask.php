<?php
/**
 * Created on 2014-04-02 by Julien Moreau (aka PixEye)
 * Last commit of this file: $Id$
 *
 * CIDR = Classless Inter-Domain Routing
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PixShellScripts
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 PixEye.net
 * @license   Affero GPL http://choosealicense.com/licenses/agpl/
 * @version   GIT: $Revision$
 * @link      https://github.com/PixEye/PixShellScripts
 * @since     Local time: $Date$
 * @filesource
 */

$charset = 'UTF-8';
$body_addon = ' onload="document.getElementById(\'form1\').reset()"';
require_once 'Include/head.php';

$ip_pattern = '([12]?\d{1,2}\.){3}[12]?\d{1,2}';
$netmask = isSet($_REQUEST['netmask'])?trim($_REQUEST['netmask']):'255.255.255.0';
$gateway = isSet($_REQUEST['gateway'])?trim($_REQUEST['gateway']):'192.168.109.1';
$ip_ranges = isSet($_REQUEST['ip_ranges'])?trim($_REQUEST['ip_ranges']):
    "192.168.109.0\r\n".
    "192.168.109.1-192.168.109.210\r\n".
    "192.168.108.0\r\n".
    "192.168.108.20-192.168.109.2\r\n".
    "192.168.109.20-192.168.109.2";

$nbl = substr_count($ip_ranges, "\n") + 2;

echo "\t<form id=\"form1\" action=\"", basename(__FILE__), "\">\n";
?>
	  <table class="nice big center grid">
	    <tbody>
	      <tr>
		<th><label for="netmask">Mask:</label></th>
		<td>
		  <input name="netmask" id="netmask" required="required" type="text"
		    value="<?php echo $netmask?>" pattern="<?php echo $ip_pattern?>"/>
		</td>
	      </tr>
	      <tr>
		<th><label for="gateway">Gateway:</label></th>
		<td>
		  <input name="gateway" id="gateway" required="required" type="text"
		    value="<?php echo $gateway?>" pattern="<?php echo $ip_pattern?>"/>
		</td>
	      </tr>
	      <tr>
		<th><label for="ip_ranges">IP range(s)*:</label></th>
		<td>
		  <textarea name="ip_ranges" id="ip_ranges" required="required"
		    rows="<?php echo $nbl?>" cols="32"
><?php echo $ip_ranges?></textarea><br/>
		  *&nbsp;1 range&nbsp;/ line.
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
/**
 * Check that an IP is in a CIDR submet
 *
 * @param string $ip           An IPv4 dotted format (a.b.c.d)
 * @param string $cidr_network A subnet in CIDR format (a.b.c.d/n)
 *
 * @return boolean
 * @link http://stackoverflow.com/a/594134/649993
 */
function ipInCidrSubnet($ip, $cidr_network)
{
    list($subnet, $bits) = explode('/', $cidr_network);
    $lip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask; // nb: in case the supplied subnet wasn't correctly aligned
    $ret = (($lip & $mask) == $subnet);

    $class = $ret?'ok':'error';
    printf(
        '%s%22s(%-17s, %-20s) =&gt; <span class="%s">%s</span>',
        "\n", 'ipInCidrSubnet', "'$ip'", "'$cidr_network'",
        $class, var_export($ret, 1)
    );

    return $ret;
}

/**
 * Transform a number of bytes to a dotted format mask
 *
 * @param int $n A positive integer
 *
 * @return string
 * @link http://stackoverflow.com/a/5857176/649993
 */
function cidr2mask($n)
{
    return long2ip(-1 << (32 - (int)$n));
}

/**
 * Transform a number of bytes to a dotted format mask
 *
 * @param int $mask A dotted format IPv4 mask
 *
 * @return int
 * @link http://www.shiftedbytes.com/2014/01/php-function-to-convert-netmask-to-cidr.html
 */
function mask2cidr($mask)
{
    $long = ip2long($mask);
    $base = ip2long('255.255.255.255');
    $cidr = 32-log(($long ^ $base)+1, 2); 
    echo "\n\t", __FUNCTION__, "('$mask') => CIDR=".var_export($cidr, 1);

    return $cidr;
}

if (''!=$ip_ranges) {
    $ip_ranges = explode("\n", $ip_ranges);
    try {
        echo "\n\t<pre>";
        $cidr = mask2cidr($netmask);

        if (!$cidr || !is_numeric($cidr)) {
            throw new ErrorException("Invalid netmask: '$netmask'!");
        }

        if ($cidr!=round($cidr)) {
            throw new ErrorException("Bad netmask: '$netmask'!");
        }

        forEach ($ip_ranges as $i => $ip_range) {
            $n = $i + 1;
            $errors = array();
            $ip_range = trim($ip_range);

            echo "\n";

            $minus_count = substr_count($ip_range, '-');
            if ($minus_count>1) {
                throw new ErrorException("$minus_count in IP range #$n!");
            } elseIf (!$minus_count) {
                $ip1 = $ip_range;
                $ip2 = $ip_range;
            } else {
                list($ip1, $ip2) = explode('-', $ip_range);
            }

            // Check IP syntax:
            if (!preg_match("|$ip_pattern|", $ip1)) {
				$errors[] = "In IP range #$n, IP1 is invalide: '$ip1'!";
            }
            if (!preg_match("|$ip_pattern|", $ip2)) {
                $errors[] = "In IP range #$n, IP2 is invalide: '$ip2'!";
            }

            $l1 = ip2long($ip1);
            $l2 = ip2long($ip2);
            if ($l1>$l2) {
                $errors[] = "In IP range #$n, $ip1 > $ip2!";

				$ipt = $ip1;
				$ip1 = $ip2;
				$ip2 = $ipt;
				$l1 = ip2long($ip1);
				$l2 = ip2long($ip2);
            }

            // Check that IP1 is in same subnet as the gateway:
            $cidr_network = "$ip1/$cidr";
            // echo "\n"; // \n\tcidr_network = '$cidr_network'";
            $res = ipInCidrSubnet($gateway, $cidr_network)?'ok':'NOK';
			if ('NOK'==$res) {
				$errors[] = "'$ip1' & '$gateway' are not in the same subnet!";
			}

            if ($ip2!=$ip1) {
                // Check that IP1 & IP2 are in the same subnet:
                // echo "\n"; // \n\tcidr_network = '$cidr_network'";
                $res = ipInCidrSubnet($ip2, $cidr_network)?'ok':'NOK';
				if ('NOK'==$res) {
					$errors[] = "'$ip1' & '$ip2' are not in the same subnet!";
				}

                // Check that IP2 is in same subnet as the gateway:
                $cidr_network = "$ip2/$cidr";
                // echo "\n"; // \n\tcidr_network = '$cidr_network'";
                $res = ipInCidrSubnet($gateway, $cidr_network)?'ok':'NOK';
				if ('NOK'==$res) {
					$errors[] = "'$ip1' & '$gateway' are not in the same subnet!";
				}
            }

			$nb_errors = count($errors);
            $s = ($nb_errors>1)?'s':'';
            $class = $nb_errors?'error':'ok';
            echo "\n\t\t<span class=\"$class\">",
                "Range #$n ($ip_range) has $nb_errors error$s.</span>";
        }

		echo "\n";
		for ($cidr=16; $cidr<=30; $cidr++) {
			$mask = cidr2mask($cidr);
			if (!($cidr%4)) {
				echo "\n\t";
			}
			printf("cidr2mask(%2d) => %-15s ", $cidr, $mask);
		}

        echo "</pre>\n";
    } catch(Exception $e) {
        $msg = $e->getMessage();
        echo "</pre>\n\n\t<p class=\"error\">$msg</p>\n";
    }
}

require_once 'Include/tail.php';

// vim: shiftwidth=4 tabstop=4 noexpandtab
