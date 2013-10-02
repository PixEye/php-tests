<?php
include_once 'lib/head.php';

/**
 * My extended XML class based on SimpleXMLElement class
 *
 * @link  http://fr.php.net/manual/fr/refs.xml.php
 * @link  http://efreedom.com/Question/1-262351/Remove-Child-Specific-Attribute-SimpleXML-PHP
 */
class XmlObj extends SimpleXMLElement
{
	/**
	 * @param XmlObj Child to be removed
	 */
	public function removeChild($child)
	{
		$dom = dom_import_simpleXml($child);
		if(!is_object($dom)) return FALSE;
		return $dom->parentNode->removeChild($dom);
	}

	/**
	 * @param string Attribute name
	 */
	public function removeAttribute($name)
	{
		$dom = dom_import_simpleXml($this);
		if(!is_object($dom)) return FALSE;
		return $dom->removeAttribute($name);
	}
}

$xml_data = "<data>\n\t<seg id='A1' extra='2'/>\n".
	"\t<seg id='B5'><tmp/></seg>\n\t<seg id='C12'/>\n</data>";
?>
	<h2>Test of my custom object: XmlObj
	  (which extends SimpleXMLElement &amp; uses DOMElement)</h2>
	
	<h3>Before:</h3>
<?php
	echo "\n<pre>".htmlSpecialChars($xml_data)."</pre>\n";
try {
	$doc = new XmlObj($xml_data);
	forEach($doc->seg as $segment)
		if($segment['id']=='C12')
			$doc->removeChild($segment);
		elseIf($segment['id']=='A1')
			$segment->removeAttribute('extra');
		else	$container = $segment;

	#$Tmp = $doc->xPath('tmp');	# does not work!
	$Tmp = $container->xPath('tmp');
	echo "\n\t<p>'tmp' tags: ".print_r($Tmp, 1)."</p>\n";
	$tmp = $Tmp[0];
	if (is_object($tmp))
		$msg = get_class($tmp);
	else	$msg = getType($tmp);
	echo "\n\t<p>First 'tmp' tag to remove is: $msg</p>\n";
	$doc->removeChild($tmp);

	$xml = $doc->asXml();
?>
	<h3>After:</h3>
<?php
	echo "\n<pre>".htmlSpecialChars($xml)."</pre>\n";
}
catch (Exception $e) {
	echo "\n\t  <div class='error'>".$e->getMessage()."</div>\n\n";
}
include_once 'lib/tail.php';
