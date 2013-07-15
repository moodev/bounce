<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */


namespace MooDev\Bounce\Proxy\CG;

/**
 * Class MethodBuilder
 * A builder for constructing methods.
 * @package MooDev\Bounce\Proxy\CG
 */
class MethodBuilder {

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
     * @param string $visibility Visibility of the method (default "public")
     * @return MethodBuilder
     */
    public static function build($name, $visibility = "public") {
        return new MethodBuilder($name, $visibility);
    }


    /**
     * @param string $name Name of the method.
     * @param string $visibility Visibility of the method (default "public")
     */
    public function __construct($name, $visibility = "public") {
        $this->_name = $name;
        $this->_visibility = $visibility;
    }

    /**
     * @param Param $param Parameter to add to the method.
     * @return MethodBuilder
     */
    public function addParam(Param $param) {
        $this->_params[] = $param;
        return $this;
    }

    /**
     * @param string $code A line of code to add to the method. You need to provide semicolons.
     * @return MethodBuilder
     */
    public function addLine($code) {
        $this->_code[] = $code;
        return $this;
    }

    /**
     * @return Method Method we've been building.
     */
    public function getMethod()
    {
        return new Method($this->_name, $this->_visibility, $this->_code, $this->_params);
    }


}