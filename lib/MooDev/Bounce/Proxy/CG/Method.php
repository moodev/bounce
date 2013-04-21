<?php


namespace MooDev\Bounce\Proxy\CG;


class Method {

    private $_name;
    private $_code = array();
    private $_params = array();
    private $_visibility = "public";

    public function __construct($name, $visibility = "public", array $code, array $params = array()) {
        $this->_name = $name;
        $this->_code = $code;
        $this->_params = $params;
        $this->_visibility = $visibility;
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function getName()
    {
        return $this->_name;
    }

    protected function _defFunction()
    {
        return "{$this->_visibility} function {$this->_name}(" . implode(", ", $this->_params) . ")";
    }

    public function __toString()
    {
        return $this->_defFunction(). " {\n" .
               "    " . implode("\n    ", $this->_code) . "\n" .
               "}\n";
    }

}