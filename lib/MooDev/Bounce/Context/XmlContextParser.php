<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Context;

use MooDev\Bounce\Exception\BounceException;
use MooDev\Bounce\Config;
use SimpleXMLElement;
use MooDev\Bounce\Xml\TypeSafeParser;

class XmlContextParser implements IContextProvider
{

    /**
     * @var TypeSafeParser a parsing helper instance
     */
    private $_beansTypeSafeParser;
    /**
     * @var TypeSafeParser a parsing helper instance
     */
    private $_phpTypeSafeParser;
    /**
     * @var string[] keeps track of all the files we're processing in nested
     * order to detect and prevent any infinite loops
     */
    protected $_importFilesStack = array();
    protected $_processedFiles = array();

    /**
     * @var ValueTagProvider[]
     */
    private $_customNamespaces = array();

    private $xmlFilePath;

    /**
     * @param $xmlFilePath
     * @param ValueTagProvider[] $customNamespaces
     * @throws \MooDev\Bounce\Exception\BounceException
     */
    public function __construct($xmlFilePath, $customNamespaces = array())
    {
        if (!file_exists($xmlFilePath)) {
            throw new BounceException("XML context not found: " . $xmlFilePath);
        }
        $this->xmlFilePath = realpath($xmlFilePath);
        $this->_customNamespaces = $customNamespaces;
    }

    public function getContext()
    {
        return $this->_parseXmlFile($this->xmlFilePath);
    }

    protected function _parseXmlFile($xmlFilePath) {
        //Check we haven't processed this file already
        if (array_key_exists($xmlFilePath, $this->_processedFiles)) {
            return $this->_processedFiles[$xmlFilePath];
        }
        //Check we haven't started processing this file already
        if (in_array($xmlFilePath, $this->_importFilesStack)) {
            throw new BounceException("Infinite recursion import detected with file $xmlFilePath");
        }
        array_push($this->_importFilesStack, $xmlFilePath);

        //Load the XML file
        $xmlContent = file_get_contents($xmlFilePath);
        //Parse into Context config
        $this->_beansTypeSafeParser = new TypeSafeParser("http://www.moo.com/xsd/bounce-beans-1.0");
        $this->_phpTypeSafeParser = new TypeSafeParser("http://www.moo.com/xsd/bounce-php-1.0");
        $contextConfig = $this->_parseConfigXml($xmlContent);
        $contextConfig->uniqueId = $xmlFilePath;
        $contextConfig->fileName = $xmlFilePath;

        //Remember that we've processed this, and pop it off the processing stack
        $this->_processedFiles[$xmlFilePath] = $contextConfig;
        array_pop($this->_importFilesStack);
        return $contextConfig;
    }

    protected function _parseConfigXml($xmlContent)
    {
        $beansXml = new SimpleXMLElement($xmlContent);
        $beansXml->registerXPathNamespace("beans", "http://www.moo.com/xsd/bounce-beans-1.0");
        $beansXml->registerXPathNamespace("php", "http://www.moo.com/xsd/bounce-php-1.0");
        //Create the config object
        $contextConfig = new Config\Context();
        //Process all imports first
        $importItems = $beansXml->xpath("beans:import");
        foreach ($importItems as $importXml) {
            $relativePath = $this->_beansTypeSafeParser->extractAttribute($importXml, "path");
            //Work out the absolute path based on the directory of the file currently being processed
            $importXmlPath = dirname($this->_importFilesStack[count($this->_importFilesStack) - 1]) . "/" . $relativePath;
            if (!is_file($importXmlPath) || !is_readable($importXmlPath)) {
                throw new BounceException("Unable to find/read file: " . $importXmlPath);
            }
            $importedContextConfig = $this->_parseXmlFile(realpath($importXmlPath));
            if (!is_null($importedContextConfig)) {
                //Add on all the beans to our context
                $contextConfig->childContexts[] = $importedContextConfig;
            }
        }

        //Go through all the bean references to create their config
        $beanItems = $beansXml->xpath("beans:bean");
        foreach ($beanItems as $beanXml) {
            $beanXml->registerXPathNamespace("beans", "http://www.moo.com/xsd/bounce-beans-1.0");
            $beanXml->registerXPathNamespace("php", "http://www.moo.com/xsd/bounce-php-1.0");
            $this->_parseAndRegisterBean($beanXml, $contextConfig);
        }
        return $contextConfig;
    }

    /**
     * Returns whether the given bean name is already registered within this context (or its children)
     *
     * @param Config\Context $context the parent context to be checked
     * @param string $beanName the name of the bean to search for in this context and all its
     * children
     * @return bool
     */
    protected function _alreadyRegistered(Config\Context $context, $beanName)
    {
        if (isset($context->beans[$beanName])) {
            return true;
        } elseif (count($context->childContexts) > 0) {
            foreach ($context->childContexts as $childContext) {
                if ($this->_alreadyRegistered($childContext, $beanName)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Parses a <bean> tag and returna a Bean opbject holding all the configuration
     * items for the bean.
     *
     * @param \SimpleXMLElement $beanXml the XML element for the Bean tag
     * @param \MooDev\Bounce\Config\Context $contextConfig
     * @throws BounceException
     * @return Config\Bean
     */
    protected function _parseAndRegisterBean(SimpleXMLElement $beanXml, Config\Context $contextConfig)
    {
        $bean = new Config\Bean();
        //Parse the items out
        $bean->class = strval($beanXml["class"]);
        $name = $beanXml["name"];
        $id = $beanXml["id"];

        $factoryBean = $beanXml["factory-bean"];
        $factoryMethod = $beanXml["factory-method"];

        $bean->factoryBean = !is_null($factoryBean) ? strval($factoryBean) : null;
        $bean->factoryMethod = !is_null($factoryMethod) ? strval($factoryMethod) : null;

        $bean->scope = isset($beanXml["scope"]) ? strval($beanXml["scope"]) : null;

        $bean->name = strval(!is_null($id) ? $id : $name);

        $lookupMethodItems = $beanXml->xpath("beans:lookup-method");
        foreach ($lookupMethodItems as $lookupMethodXml) {
            $lookupMethodXml->registerXPathNamespace("beans", "http://www.moo.com/xsd/bounce-beans-1.0");
            $lookupMethodXml->registerXPathNamespace("php", "http://www.moo.com/xsd/bounce-php-1.0");
            $lookupMethodConfig = $this->_parseLookupMethod($lookupMethodXml, $contextConfig);
            if (!is_null($lookupMethodConfig)) {
                $bean->lookupMethods[] = $lookupMethodConfig;
            }
        }

        $propertyItems = $beanXml->xpath("beans:property");
        foreach ($propertyItems as $propertyXml) {
            $propertyXml->registerXPathNamespace("beans", "http://www.moo.com/xsd/bounce-beans-1.0");
            $propertyXml->registerXPathNamespace("php", "http://www.moo.com/xsd/bounce-php-1.0");
            $propertyName = strval($propertyXml["name"]);
            $propertyValueProvider = $this->_parseProperty($propertyXml, $contextConfig);
            if (!is_null($propertyValueProvider)) {
                $bean->properties[$propertyName] = $propertyValueProvider;
            }
        }
        $constructorArgItems = $beanXml->xpath("beans:constructor-arg");
        foreach ($constructorArgItems as $constructorArgXml) {
            $constructorArgXml->registerXPathNamespace("beans", "http://www.moo.com/xsd/bounce-beans-1.0");
            $constructorArgXml->registerXPathNamespace("php", "http://www.moo.com/xsd/bounce-php-1.0");
            $propertyValueProvider = $this->_parseProperty($constructorArgXml, $contextConfig);
            if (!is_null($propertyValueProvider)) {
                $bean->constructorArguments[] = $propertyValueProvider;
            }
        }
        if (isset($bean->name) && trim($bean->name) != "") {
            if ($this->_alreadyRegistered($contextConfig, $bean->name)) {
                throw new BounceException("Duplicate bean definition: $bean->name");
            }
            $contextConfig->beans[$bean->name] = $bean;
        }
        return $bean;
    }

    protected function _createSimpleValueProvider($value)
    {
        $valueProvider = null;
        if (is_numeric($value)) {
            if (is_integer($value)) {
                $valueProvider = new Config\SimpleValueProvider(intval($value));
            } else {
                $valueProvider = new Config\SimpleValueProvider(floatval($value));
            }
        } else {
            $valueProvider = new Config\SimpleValueProvider($value);
        }
        return $valueProvider;
    }

    protected function _parseValueTag(SimpleXMLElement $element, Config\Context $contextConfig)
    {
        $valueProvider = null;
        $tagName = $element->getName();
        $nsArray = $element->getNamespaces();
        if (count($nsArray) > 0) {
            $namespace = array_shift($nsArray);
            if (isset($this->_customNamespaces[$namespace])) {
                return $this->_customNamespaces[$namespace]->getValueProvider($element, $contextConfig);
            }
        }
        if ($tagName == "value") {
            $valueProvider = $this->_createSimpleValueProvider(strval($element));
        } elseif ($tagName == "bean") {
            $ref = $this->_beansTypeSafeParser->extractAttribute($element, "ref");
            if (is_null($ref)) {
                $element->registerXPathNamespace("beans", "http://www.moo.com/xsd/bounce-beans-1.0");
                $element->registerXPathNamespace("php", "http://www.moo.com/xsd/bounce-php-1.0");
                $valueProvider = new Config\BeanValueProvider($this->_parseAndRegisterBean($element, $contextConfig));
            } else {
                $valueProvider = new Config\BeanRefValueProvider(strval($ref));
            }
        } elseif ($tagName == "null") {
            $valueProvider = new Config\NullValueProvider();
        } elseif ($tagName == "map") {
            $mapArray = array();
            $element->registerXPathNamespace("beans", "http://www.moo.com/xsd/bounce-beans-1.0");
            $mapEntryItems = $element->xpath("beans:entry");
            foreach ($mapEntryItems as $entryXml) {
                $entryName = strval($entryXml["name"]);
                $entryValueProvider = $this->_parseProperty($entryXml, $contextConfig);
                if (!is_null($entryValueProvider)) {
                    $mapArray[$entryName] = $entryValueProvider;
                }
            }
            $valueProvider = new Config\MapValueProvider($mapArray);
        } elseif ($tagName == "list") {
            $listArray = array();
            $listEntryItems = $element->xpath("*");
            foreach ($listEntryItems as $entryXml) {
                $entryValueProvider = $this->_parseValueTag($entryXml, $contextConfig);
                if (!is_null($entryValueProvider)) {
                    $listArray[] = $entryValueProvider;
                }
            }
            $valueProvider = new Config\ListValueProvider($listArray);
        } elseif ($tagName == "constant") {
            $constantName = $this->_phpTypeSafeParser->extractAttribute($element, "name");
            $valueProvider = new Config\ConstantValueProvider($constantName);
        } elseif ($tagName == "string") {
            $valueProvider = new Config\SimpleValueProvider(strval($element));
        } elseif ($tagName == "int") {
            $valueProvider = new Config\SimpleValueProvider(intval($element));
        } elseif ($tagName == "float") {
            $valueProvider = new Config\SimpleValueProvider(floatval($element));
        } elseif ($tagName == "bool") {
            $valueProvider = new Config\SimpleValueProvider(strval($element) == "true");
        } elseif ($tagName == "file") {
            $valueProvider = new Config\FilePathValueProvider(strval($element));
        }
        return $valueProvider;
    }

    protected function _parseProperty(SimpleXMLElement $propertyXml, Config\Context $contextConfig)
    {
        //Work out what kind of property this is
        $valueProvider = null;
        $valueAttr = $this->_beansTypeSafeParser->extractAttribute($propertyXml, "value");
        if (!is_null($valueAttr)) {
            //Simple value
            $value = strval($valueAttr);
            $valueProvider = $this->_createSimpleValueProvider($value);
        } else {
            //Look to see if there's a ref
            $refAttr = $propertyXml["ref"];
            if (!is_null($refAttr)) {
                $valueProvider = new Config\BeanRefValueProvider(strval($refAttr));
            } else {
                $propertyValueItems = $propertyXml->xpath("*");
                foreach ($propertyValueItems as $propertyValue) {
                    $valueProvider = $this->_parseValueTag($propertyValue, $contextConfig);
                }
            }
        }
        return $valueProvider;
    }

    protected function _parseLookupMethod(SimpleXMLElement $lookupMethodXml, Config\Context $contextConfig) {
        $nameAttr = $this->_beansTypeSafeParser->extractAttribute($lookupMethodXml, "name");
        $beanAttr = $this->_beansTypeSafeParser->extractAttribute($lookupMethodXml, "bean");

        return new Config\LookupMethod(strval($nameAttr), strval($beanAttr));

    }

}
