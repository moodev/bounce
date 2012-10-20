<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Xml;

use \SimpleXMLElement;
use MooDev\Bounce\Exception\ParserException;

/**
 * A basic type-safe XML Xml that uses SimpleXMLElement under the covers.
 * This can be used as a standalone instance to help with parsing, or can
 * be extended to create a more fluent subclass parser.
 */
class TypeSafeParser
{
    /**
     * Lookup table for various boolean values
     *
     * @var array
     */
    private $_boolLookup;

    /**
     * @var string $namespace the namespace that will be used within this parser
     */
    protected $_namespace;

    public function __construct($namespace)
    {
        $this->_namespace = $namespace;

        $this->_boolLookup = array('false' => false, 'true' => true, '0' => false, '1' => true);
    }

    /**
     * Parses an integer from the XML.
     *
     * @param $element  SimpleXMLElement the parent element from which we should
     *                  parse the child data
     * @param $tagName  string the child tag name
     * @param $required bool whether the wlement is required. If so, then
     * @return int the integer parsed from the XML
     */
    public function parseInt($element, $tagName, $required = true)
    {
        $intElement = $this->extractElement($element, $tagName, $required);

        // if null was allowed and is the result we can just return it
        if (is_null($intElement)) {
            return null;
        }

        return intval($intElement[0]);
    }

    /**
     * Parses a float value form the XML
     *
     * @param $element
     * @param $tagName
     * @param bool $required
     * @return float the float value parsed from the XML
     */
    public function parseFloat($element, $tagName, $required = true)
    {
        $floatElement = $this->extractElement($element, $tagName, $required);

        // if null was allowed and is the result we can just return it
        if (is_null($floatElement)) {
            return null;
        }

        return floatval($floatElement[0]);
    }

    /**
     * @param $element
     * @param $tagName
     * @param bool $required
     * @throws ParserException
     * @return boolean parsed from element $tagName
     */
    public function parseBool($element, $tagName, $required = true)
    {
        $boolElement = $this->extractElement($element, $tagName, $required);

        // if null was allowed and is the result we can just return it
        if (is_null($boolElement)) {
            return null;
        }

        $boolStr = strtolower($boolElement[0]);

        if (array_key_exists($boolStr, $this->_boolLookup)) {
            return $this->_boolLookup[$boolStr];
        }

        throw new ParserException("Unknown bool value: " . $boolElement->asXML());
    }

    /**
     * @param $element
     * @param $tagName
     * @param bool $required
     * @return string parsed from element $tagName
     */
    public function parseString($element, $tagName, $required = true)
    {
        $strElement = $this->extractElement($element, $tagName, $required);

        // if null was allowed and is the result we can just return it
        if (is_null($strElement)) {
            return null;
        }

        return (string)$strElement[0];
    }

    /**
     * Extract a SimpleXML element according to a particular tag name
     *
     * @param $element           \SimpleXMLElement the parent element to look in for the
     *                           child
     * @param $tagName           string the child tag name to look for
     * @param $required          bool whether the element is required or not
     * @param $returnAllElements bool whether to return all the elements found
     *                           as an array if more than one is present. If not, then an exception will
     *                           be thrown
     * @return \SimpleXMLElement|array of SimpleXMLElement the element (or array
     * of) that corresponds to the requested element et
     */
    public function extractElement(\SimpleXMLElement $element, $tagName, $required = true, $returnAllElements = false)
    {
        if (isset($this->_namespace)) {
            return $this->extractElementFromNamespace($element, $tagName, $required, $returnAllElements);
        } else {
            return $this->extractElementFromNoNamespace($element, $tagName, $required, $returnAllElements);
        }

    }

    private function extractElementFromNamespace(
        \SimpleXMLElement $element, $tagName, $required = true,
        $returnAllElements = false
    )
    {
        $element->registerXPathNamespace("default", $this->_namespace);
        $subElements = $element->xpath("default:" . $tagName);

        // should be only one element unless required = false
        switch (count($subElements)) {
            case 0:
                if ($required) {
                    throw new ParserException(
                        "Required tag $tagName not found within element: " . $element->asXML()
                    );
                } else {
                    return null;
                }
            case 1:
                return $subElements[0];
            default:
                if ($returnAllElements) {
                    return $subElements;
                } else {
                    throw new ParserException(
                        "More than one tag $tagName found within element: " . $element->asXML()
                    );
                }
        }
    }

    private function extractElementFromNoNamespace(
        \SimpleXMLElement $element, $tagName, $required = true,
        $returnAllElements = false
    )
    {

        $children = $element->children();
        $childCount = sizeof($children);
        if ($returnAllElements) {
            $childrenAsArray = array();
            foreach ($children as $child) {
                $childrenAsArray[] = $child;
            }
            return $childrenAsArray;
        }

        foreach ($children as $child) {
            /** @var $child \SimpleXMLElement */
            if ($child->getName() == $tagName) {
                return $child;
            }
        }

        if ($required) {
            //If we require that something is returned yet we have not found anything matching 
            //then we have to Error here
            throw new ParserException(
                "Required tag $tagName not found within element: " . $element->asXML()
            );

        }

        return null;
    }

    /**
     * Extract an attribute from a SimpleXML element by name
     *
     * @param \SimpleXMLElement|\SimpleXMLElement[] $element
     * @param $attributeName
     * @param string $type
     * @param null $default
     * @throws \MooDev\Bounce\Exception\ParserException
     * @return string attribute value
     */
    public function extractAttribute(\SimpleXMLElement $element, $attributeName, $type = "string", $default = null)
    {
        if (isset($element[$attributeName])) {
            $value = (string)$element[$attributeName];
        } else {
            return $default;
        }

        switch ($type) {
            case "string":
                return $value;
            case "boolean":
                $boolStr = strtolower($value);
                if (array_key_exists($boolStr, $this->_boolLookup)) {
                    return $this->_boolLookup[$value];
                }
                throw new ParserException(
                    "Unknown bool attribute value [$value] for attribute $attributeName"
                );
            default:
                return $value;
        }
    }

}
