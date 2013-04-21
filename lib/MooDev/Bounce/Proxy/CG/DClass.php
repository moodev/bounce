<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\CG;

/**
 * Class DClass
 * A Class.
 * @package MooDev\Bounce\Proxy\CG
 */
class DClass {

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string[]
     */
    private $_extends = array();

    /**
     * @var string[]
     */
    private $_implements = array();

    /**
     * @var Method[]
     */
    private $_methods = array();

    /**
     * @var string
     */
    private $_namespace = null;

    /**
     * @param string $name Name of the class
     * @param string[] $extends List of classes it extends
     * @param string[] $implements List of interfaces it implements
     * @param Method[] $methods List of methods in the class
     * @param string $namespace Namespace the class is in.
     * @param Property[] $properties List of properties on the class.
     */
    function __construct($name, $extends = array(), $implements = array(), $methods = array(), $namespace = null, $properties = array())
    {
        $this->_properties = $properties;
        $this->_extends = $extends;
        $this->_implements = $implements;
        $this->_methods = $methods;
        $this->_name = $name;
        $this->_namespace = $namespace;
    }

    public function __toString()
    {
        $str = "";
        if (isset($this->_namespace)) {
            $str .= "namespace {$this->_namespace};\n\n";
        }
        $str .= "class {$this->_name}";
        if (!empty($this->_extends)) {
            $str .= " extends " . implode(", ", $this->_extends);
        }
        if (!empty($this->_implements)) {
            $str .= " implements " . implode(", ", $this->_implements);
        }
        $str .= "{\n";
        $str .= implode("\n", $this->_properties);
        $str .= implode("\n", $this->_methods);
        $str .= "\n}\n";
        return $str;
    }

}