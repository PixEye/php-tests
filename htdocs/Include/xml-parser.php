<?php
/**
 * XMLParser Class File
 *
 * This class loads an XML document into a SimpleXMLElement that can
 * be processed by the calling application.  This accepts XML strings,
 * files, and DOM objects.  It can also perform the reverse, converting
 * an SimpleXMLElement back into a string, file, or DOM object.
 *
 * @see: http://fr.php.net/manual/fr/function.file-exists.php
 */
class XML_Parser {
    /**
     * While parsing, parse the supplied XML document.
     *
     * Sets up a SimpleXMLElement object based on success of parsing
     * the XML document file.
     *
     * @param string $doc the XML document location path
     * @return object
     */
    public static function load_file($doc) {
        if (is_readable($doc)) {
            return simplexml_load_file($doc);
        } else {
            throw new Exception ("Unable to load the XML file".
                                 " using: '$doc'", E_USER_ERROR);
        }
    }
    /**
     * While parsing, parse the supplied XML string.
     *
     * Sets up a SimpleXMLElement object based on success of parsing
     * the XML string.
     *
     * @param string $string the XML document string
     * @return object
     */
    public static function load_string($string) {
        if (isSet($string)) {
            return simplexml_load_string($string);
        } else {
            throw new Exception ("Unable to load the XML string".
                                 " using: '$string'", E_USER_ERROR);
        }
    }
    /**
     * While parsing, parse the supplied XML DOM node.
     *
     * Sets up a SimpleXMLElement object based on success of parsing
     * the XML DOM node.
     *
     * @param object $dom the XML DOM node
     * @return object
     */
    public static function load_DOM($dom) {
        if (isSet($dom)) {
            return simplexml_import_dom($dom);
        } else {
            throw new Exception ("Unable to load the XML DOM node".
                                 " using: '$dom'", E_USER_ERROR);
        }
    }
    /**
     * While parsing, parse the SimpleXMLElement.
     *
     * Sets up a XML file, string, or DOM object based on success of
     * parsing the XML DOM node.
     *
     * @param object $simplexml the simple XML element
     * @param string $type the return type (string, file, dom)
     * @param object $path the XML document location path
     * @return mixed
     */
    public static function load_SXML($simplexml, $type, $path=NULL) {
        if (isSet($simplexml) && isSet($type)) {
            switch ($type) {
                case 'string':
                    return $simplexml->asXML(); break;
                case 'file':
                    if (isSet($path)) {
                        return $simplexml->asXML($path);
                    } else {
                        throw new Exception ("Unable to create the XML file.".
                                             " Path is missing or is".
                                             " invalid: '$path'", E_USER_ERROR);
                    }
                    break;
                case 'dom':
                    return dom_import_simplexml($simplexml);
                    break;
            }
        } else {
            throw new Exception ("Unable to load the simple XML element".
                                 " using: '$simplexml'", E_USER_ERROR);
        }
    }
}
?>
