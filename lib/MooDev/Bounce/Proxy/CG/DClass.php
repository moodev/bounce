<?php


namespace MooDev\Bounce\Proxy\CG;


class DClass {

    private $_name;
    private $_extends = array();
    private $_implements = array();
    private $_methods = array();
    private $_namespace = null;

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