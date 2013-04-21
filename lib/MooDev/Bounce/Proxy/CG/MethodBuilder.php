<?php


namespace MooDev\Bounce\Proxy\CG;


class MethodBuilder {

    private $_name;
    private $_code = array();
    private $_params = array();
    private $_visibility = "public";

    public static function build($name, $visibility = "public") {
        return new MethodBuilder($name, $visibility);
    }

    public function __construct($name, $visibility = "public") {
        $this->_name = $name;
        $this->_visibility = $visibility;
    }

    /**
     * @param Param $param
     * @return MethodBuilder
     */
    public function addParam(Param $param) {
        $this->_params[] = $param;
        return $this;
    }

    /**
     * @param $code
     * @internal param \MooDev\Bounce\Proxy\CG\Param $param
     * @return MethodBuilder
     */
    public function addLine($code) {
        $this->_code[] = $code;
        return $this;
    }

    public function getMethod()
    {
        return new Method($this->_name, $this->_visibility, $this->_code, $this->_params);
    }


}