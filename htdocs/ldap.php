<?php
/**
 * Created on 2005-12-23 by Julien Moreau (aka PixEye)
 *
 * @link http://www.php.net/manual/en/ref.ldap.php
 *
 * Last commit of this file: $Id$
 */

$config_file = 'Include/ldap-config.php'; // File where you put your own LDAP config

$charset = 'UTF-8';
$title = '<abbr title="Lightweight Directory Access Protocol">LDAP</abbr>&nbsp;/ PHP request';
$head_addon = '';

$dialer_lib = 'js/webdialer.js';
if (file_exists($dialer_lib)) {
	$head_addon.= "<script type=\"text/javascript\" src=\"$dialer_lib\"></script>\n";
}

$sort_table_lib = 'js/sorttable.js';
if (file_exists($sort_table_lib)) {
	$head_addon.= "<script type=\"text/javascript\" src=\"$sort_table_lib\"></script>\n";
}

$body_addon = ' onload="document.getElementById(\'form1\').reset()"';
include_once 'Include/head.php';

// Basic configuration:
$ldap_server = 'localhost';
$base_dn = 'o=My Company,c=FR';
$search_dn = "ou=Users,$base_dn";
//$filter = '(objectClass=person)';	// very simple filter
//$filter = '(&(objectCategory=person)(objectClass=user))'; // All user objects from an AD
$filter = '(&(objectCategory=person)(objectClass=user)(mail=*@*))';
$ldap_version = 3;
//$rdn = 'me';
//$bind_dn = "$rdn,$base_dn";
//$pw = 'fake';
$list_height = '330px';

@include_once $config_file;	// <-- your own configuration here (overwrite previous vars)

if (isSet($_REQUEST['filter']) && trim($_REQUEST['filter'])!='') $filter = $_REQUEST['filter'];
if (isSet($_REQUEST['uid']) && trim($_REQUEST['uid'])!='') $filter = '(uid='.$_REQUEST['uid'].')';
if (isSet($_REQUEST['cn']) && trim($_REQUEST['cn'])!='') $filter = '(cn='.$_REQUEST['cn'].')';
if (isSet($_REQUEST['dn']) && trim($_REQUEST['dn'])!='')
{
	$dn = trim($_REQUEST['dn']);
	$pos = strPos($dn, ',');
	if (false===$pos) $filter = $dn;
	else {
		$filter = subStr($dn, 0, $pos);
		$search_dn = subStr($dn, 1+$pos);
	}
}

$current_day = date('d');
$current_month = date('m');
$current_year = date('Y');
$next_month = $current_month%12+1;

function first_word($s) { $Word = explode(' ', $s); return $Word[0]; }

// Base LDAP sequence is:
//	connect, bind, search, result parsing, deconnect

echo "\t<div>Connecting to server: '$ldap_server'... the result is: <output>";
$time_connect = microtime(true);
$link_id = ldap_connect($ldap_server);	// Must be a valid LDAP server
$time_connect = round(1000*(microtime(true) - $time_connect));
echo "$link_id</output></div>\n\n";

$msg = "ldap_connect('$ldap_server') took:\t$time_connect ms.";
if (!isSet($argv)) $msg = "\t<div class=\"discret\">$msg<br/><br/></div>";
echo $msg, PHP_EOL;

if (!$link_id) {
	echo "\t<h3 class=\"error\">Unable to connect to LDAP server '$ldap_server'!</h3>\n\n";
} else {
	$is_ok = true;
	//$is_ok = ldap_set_option($link_id, LDAP_OPT_REFERRALS, 0);
	if (!$is_ok)
		echo "\t<div class=\"error\">Unable to set option LDAP_OPT_REFERRALS to 0!</div>\n\n";

	if (isSet($ldap_version)) {
		$is_ok = ldap_set_option($link_id, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);
		if (!$is_ok)
			echo "\t<div class=\"error\">Unable to force the LDAP version",
		   		" to: '$ldap_version'.</div>\n";
	}

	$is_ok = ldap_get_option($link_id, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);
	if ($is_ok) echo "\t<div>Using LDAP version: <strong>$ldap_version</strong>.</div>\n";
	else echo "\t<div class=\"error\">Unable to find the version of the LDAP protocol!</div>\n";

	if (isSet($pw) && (isSet($rdn) || isSet($bind_dn))) {
		if (!isSet($bind_dn)) $bind_dn = "$rdn,$base_dn";
		echo "\t<div>Binding as '$bind_dn'... the result is:\n\t <strong>";
		$time_bind = microtime(true);
		$is_ok = @ldap_bind($link_id, $bind_dn, $pw); // connect with credentials
	}
	else {
		echo "\t<div><span class=\"warn\">Anonymous binding...</span>",
			" the result of the authentication is:\n\t <strong>";
		$time_bind = microtime(true);
		$is_ok = @ldap_bind($link_id);	// anonymous connection
	}
	$time_bind = round(1000*(microtime(true) - $time_bind));

	if (false===$is_ok)
		echo "<span class=\"error\">false (NOK)!<br/>\n\t\tError: ",
			ldap_error($link_id), "</span></strong></div>\n\n";
	else {
		echo "<span class=\"ok\">true (ok)</span></strong></div>\n\n";
		echo "\t<div class=\"discret\">ldap_bind() took:\t$time_bind ms.</div>\n";

		if (!isSet($search_dn)) $search_dn = $base_dn;
		echo "\t<form id=\"form1\" action=\"", basename(__FILE__), "\">\n";
		echo "\t  ", '<div>Searching <input type="text" name="filter" size="80" value="',
			htmlEntities($filter), '"/>',
			"\n\t  ", '<input type="submit" value="Apply"/>', "<br/>\n";
		echo "\t  ", htmlEntities("From '$search_dn'..."),
			"\n\t  the result of this search is:\n\t  ";
		// Search by name:
		$time_search = microtime(true);
		$search_result = @ldap_search($link_id, $search_dn, $filter);
		$time_search = round(1000*(microtime(true) - $time_search));

		if (false===$search_result)
			echo "<span class=\"error\">false (NOK)!<br/>\n\t\tError: ",
				ldap_error($link_id), "</span></div>\n";
		else {
			echo "$search_result</div>\n\n";
			echo "\t<div class=\"discret\">ldap_search() took:\t",
				$time_search, " ms.<br/><br/></div>\n";

			$nb_results = ldap_count_entries($link_id, $search_result);
			echo "\t<div>Found <strong>$nb_results entries</strong>.</div>\n\n";

			// Sort entries:
			$sort_field = 'cn';
			echo "\t<div>Sorting by '$sort_field'... the result of this sort is:\n\t  <output>";
			$time_sort = microtime(true);
			$is_ok = ldap_sort($link_id, $search_result, $sort_field)?'true':'false';
			$time_sort = round(1000*(microtime(true) - $time_sort));
			echo "$is_ok</output></div>\n\n";
			echo "\t<div class=\"discret\">ldap_sort(link_id, Result, '$sort_field') took:\t",
				$time_sort, " ms.</div>\n";

			// Get data of entries:
			$get_data_time = microtime(true);
			$Entries = ldap_get_entries($link_id, $search_result);
			$get_data_time = round(1000*(microtime(true) - $get_data_time));
			echo "\t<div class=\"discret\"><br/>ldap_get_entries(link_id, Result) took:\t",
				$get_data_time, " ms.</div>\n";
			$count = $Entries['count'];
			echo "\t<div>Reading entries... data for $count entries:</div>\n\n";

			//echo "\t<pre>", var_export($Entries, true), "</pre>\n\n"; 	//for debug

			if ($count != $nb_results) {
				echo "\t<div class=\"error\">count = $count =!= $nb_results!</div>\n";
            }

            echo "\t<table class=\"nice centered sortable grid\">\n";
			echo "\t  <thead>\n";
			echo "\t    <tr>\n";
			echo "\t      <th>Prénom</th>\n";
			echo "\t      <th>Nom</th>\n";
			echo "\t      <th>Email</th>\n";
			echo "\t      <th>Téléphone</th>\n";
			echo "\t      <th>Création</th>\n";
			echo "\t      <th>Extra</th>\n";
			echo "\t    </tr>\n";
			echo "\t  </thead>\n";
			echo "\t  <tfoot>\n";
			echo "\t    <tr>\n";
			echo "\t      <th>Prénom</th>\n";
			echo "\t      <th>Nom</th>\n";
			echo "\t      <th>Email</th>\n";
			echo "\t      <th>Téléphone</th>\n";
			echo "\t      <th>Création</th>\n";
			echo "\t      <th>Extra</th>\n";
			echo "\t    </tr>\n";
			echo "\t  </tfoot>\n";
			echo "\t  <tbody>\n";
			$base_link = basename($_SERVER['PHP_SELF']);
			$Birthdays = Array();
			$NextBirthdays = Array();
			forEach($Entries as $Entry)
			{
				if (!is_array($Entry)) continue;

				$dn = $Entry['dn'];
				$cn = $Entry['cn'][0];
                $org = isSet($Entry['o'])?$Entry['o'][0]:'';
                $login = isSet($Entry['uid'])?$Entry['uid'][0]:$Entry['cn'][0];
				  $given_name = isSet($Entry['givenname'])?$Entry['givenname'][0]:'';
                 $family_name = isSet($Entry['sn'])?$Entry['sn'][0]:'';
				$display_name = isSet($Entry['displayname'])?$Entry['displayname'][0]:'';
                $email = isSet($Entry['mail'])?$Entry['mail'][0]:'';

                $creation_date = isSet($Entry['whencreated'])?
                    DateTime::createFromFormat(
                        'YmdGis', subStr($Entry['whencreated'][0], 0, 14)
                    ):null;
                $creation_date = is_object($creation_date)?
                    $creation_date->format('Y-m-d H:i:s'):'';

                if (!isSet($Entry['oxuserinstantmessenger'])) $im1 = '';
                else $im1 = first_word($Entry['oxuserinstantmessenger'][0]);
                if (!isSet($Entry['oxuserinstantmessenger2'])) $im2 = '';
                else $im2 = first_word($Entry['oxuserinstantmessenger2'][0]);

				// Phones:
				$tel = isSet($Entry['telephonenumber'])?$Entry['telephonenumber'][0]:'';
				$mobile = isSet($Entry['mobile'])?$Entry['mobile'][0]:'';
				$ipphone = isSet($Entry['ipphone'])?$Entry['ipphone'][0]:'';

				if (!isSet($Entry['birthday'])) $birthday = ''; else {
					$birthday = $Entry['birthday'][0];
					$birthyear = substr($birthday, 0, 4);
					$age = $current_year - $birthyear;
					if ($birthday!='1970-01-01' && $age>15 && $age<66) {
						$birthday_day = substr($birthday, -2, 2);
						$birthmonth = substr($birthday, 5, 2);
						if ($birthmonth==$current_month) {
							if ($birthday_day==$current_day)
								$Birthdays[] = "<a href=\"mailto:$email\">$display_name</a>".
									" <span class=\"surligne\">(the $birthday_day/".
									"$birthmonth/$birthyear&nbsp;- $age ans)</span>";
							else	$Birthdays[] = "<a href=\"mailto:$email\">$display_name</a> ".
									"(the $birthday_day/$birthmonth/$birthyear&nbsp;- $age ans)";
						} elseif ($birthmonth==$next_month)
							$NextBirthdays[] = "<a href=\"mailto:$email\">$display_name</a>".
								" (the $birthday_day/$birthmonth/$birthyear&nbsp;- $age ans)";
					}
				}

				//$link = "$base_link?uid=".urlEncode($login);
				$link = "$base_link?dn=".urlEncode($dn);
				//$link = "$base_link?cn=".urlEncode($cn);
                // you may add: ($login @ $org)";

                echo "\n\t  <tr>";
                echo "\n\t    <td class=\"center\"><a href=\"$link\">$given_name</a></td>";
                echo "\n\t    <td class=\"center\"><a href=\"$link\">$family_name</a></td>";
                echo "\n\t    <td class=\"right\"><a href=\"mailto:$email\">$email</a></td>";
                echo "\n\t    <td class=\"center\"><a href=\"tel:$tel\">$tel</a></td>";
                echo "\n\t    <td><small>$creation_date</small></td>";
				if (isSet($Entry['mailenabled']) && $Entry['mailenabled'][0]!='OK')
					echo "\n\t    <td class=\"gone\"><span class=\"icons\"><img".
						" alt=\"Email disabled: $email\"".
						" title=\"Email disabled: $email\"".
						" src=\"Images/mail-grey.gif\"$sl>";
				else
					echo "\n\t    <td><span class=\"icons\"><a href=\"mailto:$email\"".
						" title=\"Send an email to: $email\"><img".
						"\n\t\t alt=\"Email: $email\" src=\"Images/mail.gif\"$sl></a>";

				if (trim($tel)!='') echo "\n\t\t<a href=\"#\"".
					" title=\"Call: $tel\" onclick=\"return launchWebDialerServlet('$tel')\"><img\n".
					"\t\t alt=\"phone\" src=\"Images/telephone.png\"$sl></a>";
				else echo "\n\t\t<img alt=\"Phone NA\" title=\"Phone number NA\" src=\"Images/telephone-grey.png\"$sl>";

				if (trim($mobile)!='') echo "\n\t\t<a href=\"#\"".
					" title=\"Call: $mobile\" onclick=\"return launchWebDialerServlet('$mobile')\"><img\n".
					"\t\t alt=\"mobile phone\" src=\"Images/mobile.gif\"$sl></a>";
				else echo "\n\t\t<img alt=\"Mobile phone NA\" title=\"Mobile number NA\" src=\"Images/mobile-grey.gif\"$sl>";

				if (trim($ipphone)!='') echo "\n\t\t<a href=\"#\"".
					" title=\"Call: $ipphone\" onclick=\"return launchWebDialerServlet('$ipphone')\"><img\n".
					"\t\t alt=\"IP phone\" src=\"Images/ip-phone.png\"$sl></a>";
				else echo "\n\t\t<img alt=\"IP phone NA\" title=\"IP phone number NA\" src=\"Images/ip-phone-grey.png\"$sl>";

				if (trim($im2)=='' or strstr($im2, 'jabber'))
					{ $tmp = $im1; $im1 = $im2; $im2 = $tmp; }

				if (trim($im1)!='') {
					echo "\n\t\t<a href=\"mailto:$im1\" title=\"Send an email to: $im1\"><img\n".
						 "\t\t alt=\"Jabber is: $im1\" src=\"Images/Jabber-lightbulb.png\"$sl></a>";
				} else echo "\n\t\t<img alt=\"Jabber NA\" src=\"Images/Jabber-lightbulb-grey.png\"$sl>";

				if (trim($im2)!='') echo "\n\t\t<a href=\"mailto:$im2\" title=\"Send an email to: $im2\"><img\n".
						 "\t\t alt=\"MSN is: $im2\" src=\"Images/msn.png\"$sl></a>";
				else echo "\n\t\t<img alt=\"MSN NA\" src=\"Images/msn-grey.png\"$sl>";

				if (trim($birthday)!='' && $birthday!='1970-01-01') echo "\n\t\t<img alt=\"birthday:".
					" $birthday\" title=\"Birthday: $birthday\" src=\"Images/birthday.png\"$sl>";
				else echo "\n\t\t<img alt=\"Birthday: NA\" title=\"NA\" src=\"Images/birthday-grey.png\"$sl>";

				// Address:
				if (array_key_exists('l', $Entry) && trim($Entry['l'][0])!='') {
					$address = '';
					if (isSet($Entry['street']))
						$address = $Entry['street'][0].', ';
					if (isSet($Entry['streetaddress']))
						$address = $Entry['streetaddress'][0].', ';
					if (isSet($Entry['postalcode']))
						$address.= $Entry['postalcode'][0].' ';
					$address.= $Entry['l'][0];
					if (isSet($Entry['co']))
						$address.= ', '.$Entry['co'][0];
					elseIf (isSet($Entry['usercountry']))
						$address.= ', '.$Entry['usercountry'][0];

					$enc_addr = urlEncode("$address ($display_name [$org])");
					echo "\n\t\t<a title=\"See map of: $address\"".
						" href=\"http://maps.google.fr/maps?f=q&amp;hl=fr&amp;q=$enc_addr\"".
						"><img\n\t\t alt=\"Address: $address\" src=\"Images/map.gif\"$sl></a>";
                } else {
                    echo "\n\t\t<img alt=\"Address not available\"".
						" title=\"Address not available\"".
						" src=\"Images/map-grey.gif\"$sl>";
                }
				echo '</span>';

				if (1 == $count) {
					$Resume = array();
					forEach($Entry as $k => $v)
						if (!is_numeric($k)) {
							if (is_array($v)) {
								if (isSet($v['count'])) unset($v['count']);
								$v = implode(', ', $v);
							}
                            $Resume[$k] = ctype_print($v)?utf8_encode($v):$v;
						}
					ksort($Resume);
					echo "\n\n\t<pre>Resume = ";
					var_export($Resume);
					echo "</pre>\n";
				}
				echo "</td></tr>\n";
			} // endForEach
			echo "\t  </tbody>\n";
			echo "\t</table>\n";

			$nb_birthday = count($Birthdays);
			$nb_next_birthday = count($NextBirthdays);
			if ($nb_birthday>0 || $nb_next_birthday>0) {
				echo "\t<div class=\"rightBox\" style=\"top:150px; width:300px\">\n";
				echo "\t\t<div class=\"title\">Anniversaire(s) proche(s)</div>\n";
				echo "\t\t<img alt=\"Cake\" src=\"Images/birthday.png\"$sl> ".
					"<strong>Anniversaire(s) du mois:</strong><br/>\n";
				if ($nb_birthday>0) {
					forEach($Birthdays as $val) echo "\t\t$val.<br/>\n";
				} else	echo "\t\tAucun.<br/>\n";
				echo "\t\t<br/>\n";
				echo "\t\t<strong>Anniversaire(s) du mois suivant:</strong><br/>\n";
				if ($nb_next_birthday>0) {
					forEach($NextBirthdays as $val) echo "\t\t$val.<br/>\n";
				} else	echo "\t\tAucun.<br/>\n";
				echo "\t</div>\n";
			}
		}
        echo "\t</form>\n";
	}

	echo "\t<br/>\n\n";
	echo "\t<div>Disconnecting from server: '$ldap_server'... the result is: <output>";
	$time_close = microtime(true);
	$is_ok = ldap_unbind($link_id)?'true':'false';
	$time_close = round(1000*(microtime(true) - $time_close));
	echo "$is_ok</output></div>\n\n";
	echo "\t<div class=\"discret\">ldap_unbind() took:\t$time_close ms.<br/><br/></div>\n";
}

include_once 'Include/tail.php';

// vim: shiftwidth=4 tabstop=4
