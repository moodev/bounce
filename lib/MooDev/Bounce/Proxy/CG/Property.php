<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Proxy\CG;

/**
 * Class Property
 * A property of a class.
 * @package MooDev\Bounce\Proxy\CG
 */
class Property {

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_default = "null";

    /**
     * @var string
     */
    private $_visibility = "public";

    /**
     * @param string $name Name of the property.
     * @param string $default String of the default value, defaults to "null".
     * @param string $visibility Visibility of the property, defaults to "public".
     */
    function __construct($name, $default = "null", $visibility = "public")
    {
        $this->_default = $default;
        $this->_name = $name;
        $this->_visibility = $visibility;
    }

    public function __toString()
    {
        return "{$this->_visibility} \${$this->_name} = {$this->_default};\n";
    }

}