<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\CG;

/**
 * Class Method
 * A method.
 * @package MooDev\Bounce\Proxy\CG
 */
class Method {

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string[]
     */
    private $_code = array();

    /**
     * @var Param[]
     */
    private $_params = array();

    /**
     * @var string
     */
    private $_visibility = "public";

    /**
     * @param string $name Name of the method.
     * @param string $visibility Visibility of the method. Default is "public"
     * @param string[] $code Array of lines of code.
     * @param Param[] $params List of params this method is called with.
     */
    public function __construct($name, $visibility = "public", array $code, array $params = array()) {
        $this->_name = $name;
        $this->_code = $code;
        $this->_params = $params;
        $this->_visibility = $visibility;
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