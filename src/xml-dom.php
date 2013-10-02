<?php
include_once 'lib/head.php';

// @see: http://efreedom.com/Question/1-262351/Remove-Child-Specific-Attribute-SimpleXML-PHP

$data='<data>
	<seg id="A1"/>
	<seg id="A5"/>
	<seg id="A12"/>
	<seg id="A29"/>
	<seg id="A30"/>
</data>';
?>
	<h2>Test of the objects: SimpleXMLElement &amp; DOM</h2>
	
	<h3>Before:</h3>
<?php
	echo '<pre>'.htmlSpecialChars($data)."</pre>\n\n";
try {
	$doc = new SimpleXMLElement($data);
	foreach($doc->seg as $seg)
	{
		if($seg['id'] == 'A12') {
			# SimpleXMLElement is not able to remove a child but DOM...
			$dom = dom_import_simplexml($seg);
			$dom->parentNode->removeChild($dom);
		}
	}
	$xml = $doc->asXml();
?>
	<h3>After:</h3>
<?php
	echo '<pre>'.htmlSpecialChars($xml)."</pre>\n\n";
}
catch (Exception $e) {
	echo "\t  <div class='error'>".$e->getMessage()."</div>\n\n";
}
include_once 'lib/tail.php';
