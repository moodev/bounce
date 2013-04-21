<?php


namespace MooDev\Bounce\Proxy\CG;


class Property {
    private $_name;
    private $_default = null;
    private $_visibility = "public";

    function __construct($name, $default = null, $visibility = "public")
    {
        $this->_default = $default;
        $this->_name = $name;
        $this->_visibility = $visibility;
    }

    public function __toString()
    {
        $str = "{$this->_visibility} \${$this->_name} = ";
        if ($this->_default) {
            $str .= $this->_default;
        } else {
            $str .= "null";
        }
        $str .= ";\n";
        return $str;
    }

}