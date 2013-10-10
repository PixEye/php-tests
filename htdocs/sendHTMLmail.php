<?php
$charset = 'utf-8';
$title = "Multipart email send test (plain text &amp; HTML)";
include_once 'Include/head.php';

$lq = '&laquo;&nbsp;';
$rq = '&nbsp;&raquo;';
$PHP_SELF = $_SERVER['PHP_SELF'];
$script = basename($PHP_SELF, '.php');
$script_filename = basename($PHP_SELF);
$host = $_SERVER['SERVER_NAME'];
$method = 'post';
$src_array_name = '_REQUEST';	# '_'.strToUpper($method);
$Result = $$src_array_name;
$nb_links = 0;

function in_one_line($s) { return preg_replace('|\s+|', ' ', $s); }
function br2nl($s) { return preg_replace('|<br/?>|i', "\n", $s); }

function html_links_to_txt($s)
{
	global $nb_links;
	return preg_replace(
		'|<a\s+.*href="([^"]*)"[^>]*>([^<]*)</a>|i',
		"$2 [$1]", $s, -1 , $nb_links);
}

function html2txt($s, $keep_html_entities = FALSE)
{
	$tmp = preg_replace('|^.*<body[^>]*>\s*|is', '', $s);
	$tmp = html_links_to_txt($tmp);
	$tmp = strip_tags(br2nl(in_one_line($tmp)));
	$tmp = trim(str_replace("\n ", "\n", $tmp));
	$tmp = trim(str_ireplace('&euro;', 'E', $tmp));
	$tmp = wordWrap($tmp);
	if ($keep_html_entities) return $tmp;
	return html_entity_decode($tmp);
}

$send = true; $mail_sent = false;
forEach(array('from', 'to', 'subject', 'html_body') as $var)
{
	if (isSet($Result[$var]))
		$$var = stripSlashes($Result[$var]);
	else
	{
		$send = false;
		echo "\t<!-- Parameter not set in \$$src_array_name: '$var'. -->\n";
	}
}

if ($send)
{
	$boundary = md5(uniqId(time()));
	$plain_body = html2txt($html_body);
	$html_body = "<html>\n  <head>\n".
		"    <title>".htmlEntities($subject)."</title>\n".
		"  </head>\n  <body style=\"background:#fff; color:#000\">\n".
		"    $html_body\n".
		"  </body>\n</html>\n";

	forEach(Array('to', 'cc', 'bcc') as $Tab)
		if (is_array($Tab)) {
			$Dest = Array();
			forEach($Tab as $DestCoords)
				$Dest[] = $DestCoords[0].' '.$DestCoords[1].' <'.$DestCoords[2].'>';
			$Tab = implode(', ', $Dest);
			unset($Dest);
		}

	// Headers:
	$headers = '';
#	$enc_subject = "=?$charset?Q?".imap_qprint($subject).'?=';
	$enc_subject = "=?$charset?B?".base64_encode($subject).'?=';
	$enc_subject = in_one_line($enc_subject);

	//	main ones:
#	$headers.= "From: Julien Moreau <jmoreau@example.com>\r\n";
#	$headers.= "Subject: $enc_subject\r\n";
#	$headers.= "To: $to\r\n";
	if (isSet( $cc) && trim( $cc)!='') $headers.= "Cc: $cc\r\n";
	if (isSet($bcc) && trim($bcc)!='') $headers.= "Bcc: $bcc\r\n";

	//	priority:
	if (!isSet($priority)) $priority = '';
	$priority = trim($priority);
	switch($priority) {
		case '1':
		case '2':
		case '3':
		case '4':
		case '5':
			$headers.= "X-Priority: $priority\r\n";
	}

	// In order to send HTML email, there should be a content-type header:
	$headers.= "MIME-Version: 1.0\n";
	$headers.= 'X-Mailer: PHP '.PHP_VERSION."\n";

	$headers.= "Content-Type: multipart/alternative; boundary=\"$boundary\"\n\n";
	$headers.= "$plain_body\n--$boundary\n";
	$headers.= "Content-Type: text/plain; charset=$charset\n";
	$headers.= "Content-Transfer-Encoding: 8bit\n\n";
	$headers.= "$plain_body\n--$boundary\n";
	$headers.= "Content-Type: text/HTML; charset=$charset\n";
	$headers.= "Content-Transfer-Encoding: 8bit\n\n";
	$headers.= "$html_body\n--$boundary--\n"; 

	// Send here:
	$mail_sent = mail($to, $enc_subject, '', $headers);
	syslog(LOG_INFO, "Mail sent from $script_filename ? ".($mail_sent?'yes':'NO'));
#	echo "\t<!-- mail($to, $enc_subject, message,\n$headers) returns: $mail_sent -->\n";
}

// Flex does not encode HTML accents
$html_signature = "<b>Your NAME</b><br/>
Title<br/>
Service<br/>
<a href=\"http://www.example.com/\">Company</a><br/>
Address<br/>
Disclaimer.";

$default_html_body = "<b>H</b>ello,<br/>
<br/>
This is a multipart email (plain text &amp; HTML).<br/>
Thus, there can be <b><span style=\"color:#080\">colors</span></b>,
other <strong>styles</strong> &amp; even
<a href=\"http://www.example.com/\">links</a>.<br/>
<br/>
Regards,<br/>
<span style=\"background:#fff; color:#888\" class=\"signature\">-- <br/>\n$html_signature</span>";

@include_once 'Include/mail-config.php'; # <-- your $html_signature & $default_html_body in this file

if (!isSet($subject) || ''==trim($subject))
	$subject = "Test of $script_filename from $host";
if (!isSet($from) || ''==trim($from))
	$from = "$script@$host";
?>
	<h2>Compose your message:</h2>

	<form action="<?php echo $script_filename?>" method="<?php echo $method?>" name="sendmail" enctype="multipart/form-data">
	  <div class="center">
	    <label>From: <input type="email" name="from" required
		value="<?php echo isSet($from)?$from:''?>"<?php echo $sl?>></label>
	    <br<?php echo $sl?>>
	    <label>Subject:
	      <input type="text" name="subject" size="60" required
		value="<?php echo $subject?>"<?php echo $sl?>></label>
	    <label>Priority:
	      <select name="priority">
		<option value="1">Hight</option>
		<option value="3" selected>Normal</option>
		<option value="5">Low</option>
	      </select>
	    </label>
	    <br<?php echo $sl?>>
	    <label>To: <input type="email" name="to" required
		value="<?php echo isSet($to)?$to:''?>"<?php echo $sl?>></label>
	    <br<?php echo $sl?>>
	    <input type="submit" style="float:right" value="Send"<?php echo $sl?>>
	    <label>Message:
	    <br<?php echo $sl?>>
	      <textarea name="html_body" rows="12" cols="90"
><?php echo htmlSpecialChars($default_html_body)?></textarea></label>
	    <br<?php echo $sl?>>
	    <input type="submit" style="float:right" value="Send"<?php echo $sl?>>
	    <div>src_array_name = '<?php echo $src_array_name?>':
		Send = <?php echo $send?'true':'false';
		if ($send) {
			echo ":\nmail_sent = ";
			$to = htmlEntities($to);
			echo $mail_sent?"<span class=\"ok\">true</span> (mailto: $to)":
					'<span class="error">false</span>';
		} ?>.</div>
	  </div>

	  <h2>Previews:</h2>

	  <div id="rich_view"
	    style="text-align:left; width:46%; border:inset 1px; padding:5px; float:left">
<?php echo "$default_html_body\n"?>
	  </div>

	  <pre id="text"
	    style="text-align:left; width:46%; border:inset 1px; padding:5px; float:left; margin:0 9px"
><?php echo html2txt($default_html_body, TRUE)?></pre>
	</form>
<?php
include_once 'Include/tail.php';
// vim: tabstop=8 shiftwidth=8 noexpandtab
