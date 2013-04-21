<?php


namespace MooDev\Bounce\Proxy\CG;


class CallBuilder {

    private $_params = array();
    private $_what;

    public function __construct($what) {
        $this->_what = $what;
    }

    public static function build($what) {
        return new CallBuilder($what);
    }

    public function addParam($name) {
        $this->_params[] = $name;
        return $this;
    }

    public function getCall() {
        return new Call($this->_what, $this->_params);
    }

}