<?php


namespace MooDev\Bounce\Proxy\CG;


class Param {
    private $_name;
    private $_default = null;
    private $_optional = false;
    private $_typeHint = true;

    function __construct($name, $optional = false, $default = null, $typeHint = null)
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
            $str .= " = ";
            if ($this->_default) {
                $str .= $this->_default;
            } else {
                $str .= "null";
            }
        }
        return $str;
    }

}