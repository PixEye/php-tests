<?php
include_once 'lib/head.php';
?>
	<h2>Test de l'objet&nbsp;: SimpleXMLElement</h2>
<?php
try {
	// Building XML:
	echo "\t<p>Building our own XML:</p>\n\n";
	//	Set the root:
#	$Pool = new SimpleXMLElement('<pool/>');
	$Pool = new SimpleXMLElement(
		"<pool>\n\t<name>%%ZVOL%%</name>\n\t<source/>\n\t<target/>\n</pool>");
	$Pool->addAttribute('type', 'iscsi');

	//	Add the children:
#	$Pool->addChild('name', '%%ZVOL%%');
#	$Source = $Pool->addChild('source');
	$Sources = $Pool->xPath('source'); $Source = $Sources[0]; unset($Sources);
	$Host = $Source->addChild('host');
	$Host->addAttribute('name', '%%SAN_IP%%');
	$Device = $Source->addChild('device');
	$Device->addAttribute('path', '%%PATH%%');
	$Device->addAttribute('transportprotocol', 'tcp');
	$Targets = $Pool->xPath('target'); $Target = $Targets[0]; unset($Targets);
	$Path = $Target->addChild('path', '/dev/disk/by-path');

	//	Get & display the XML:
	$xml = $Pool->asXML();
	echo '<pre>'.htmlSpecialChars($xml)."</pre>\n\n";

	// Parsing XML:
	include_once 'lib/xml-parser.php';
	$XP = new XML_Parser;
	$ns = 'ovf';
	$target_type = 'string';
	#$XML_files = Array('pool-config.xml', 'vm-config-paravirt.xml', 'vm-config.xml');
	$XML_files = Array('pool-config.xml', 'vm-config.xml');
#	$XML_files = Array('Examples/WindowsXP.ovf'); // test for OVF support
	forEach($XML_files as $filename) {
		if ($filename==basename($filename))
			$filename = '/etc/cloud-admin/'.$filename;
		echo "\t<hr/><!-- _____________________________ -->\n\n";
		echo "\t<p>Loading XML file '$filename'...\n";
		$SXMLE = $XP->load_file($filename);
		echo "\t  type of the result is:";
		$type = getType($SXMLE);
		if (is_object($SXMLE))
			echo "\t  <span class='ok'>$type  <img alt='(ok)' src='Images/tick.png'/></span><br/>\n";
		else	echo "\t  <span class='error'>$type <img alt=':(' src='Images/warn.png'/></span><br/>\n";

		$xml = $XP->load_SXML($SXMLE, $target_type);
		echo "\t  type of the result is:";
		$type = getType($xml);
		if (is_string($xml))
			echo "\t  <span class='ok'>$type   <img alt='(ok)' src='Images/tick.png'/></span>";
		else	echo "\t  <span class='errork'>$type <img alt=':(' src='Images/warn.png'/></span>";
		echo "</p>\n\n<pre>".htmlSpecialChars($xml)."</pre>\n\n";

		echo "\tParsing the XML of '$filename'...<br/>\n";
		$xml_root = $SXMLE->getName();
		echo "\t  XML <strong>root</strong> node is: <strong>$xml_root</strong>";
		$RootAttr = $SXMLE->attributes();
		if (isSet($RootAttr['type']) && ''!=trim($RootAttr['type']))
			echo ' (type="'.$RootAttr['type'].'")';
		echo "<br/>\n";

		foreach ($SXMLE->children() as $child) {
			$child_name = $child->getName();
			echo "\t  XML child node is: <strong>$child_name</strong>";

			$val = $child->$child_name;
			if (is_string($val))
				echo " ($val)";
			elseif (is_object($val))
				echo ' ('.get_class($val).' object)';
			else
				echo ' ('.getType($val).')';
			echo "<br/>\n";

			if ('References'==$child_name) {
				$Baby = $child->children();
				$File = $Baby[0];
				forEach($File->attributes($ns, TRUE) as $k => $v)
					echo "\t    `-> $k='$v'<br/>\n";
			} elseif ('devices'==$child_name) {
				$Baby = $child->children();
				$Disk = $Baby[2];
				$DiskProperties = $Disk->children();
				forEach($DiskProperties as $DiskProperty) {
					echo "\t    `-> ".$DiskProperty->getName().':';
					forEach($DiskProperty->attributes() as $k => $v)
						echo " $k='$v',";
					echo "<br/>\n";
				}
			}
		}
	}
} catch (Exception $e) {
	echo "\t  <span class='error'>".$e->getMessage()."</span></p>\n\n";
}
include_once 'lib/tail.php';
