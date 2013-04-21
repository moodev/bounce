<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\CG;

/**
 * Class Param
 * A parameter to a method.
 * @package MooDev\Bounce\Proxy\CG
 */
class Param {

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_default;

    /**
     * @var bool
     */
    private $_optional = false;

    /**
     * @var string
     */
    private $_typeHint;

    /**
     * @param string $name Name of the parameter (without the $)
     * @param bool $optional True if it's optional.
     * @param string $default String of the default value. Defaults to "null"
     * @param string $typeHint String of the type hint to add.
     */
    function __construct($name, $optional = false, $default = "null", $typeHint = null)
    {
        $this->_default = $default;
        $this->_name = $name;
        $this->_optional = $optional;
        $this->_typeHint = $typeHint;
    }

    public function __toString()
    {
        $str = "";
        if ($this->_typeHint) {
            $str .= $this->_typeHint . " ";
        }
        $str .= "\${$this->_name}";
        if ($this->_optional) {
            $str .= " = " . $this->_default;
        }
        return $str;
    }

}