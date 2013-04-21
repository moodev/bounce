<?php


namespace MooDev\Bounce\Proxy\CG;


class Call {

    private $_what;
    private $_params = array();

    public function __construct($what, array $params = array()) {
        $this->_what = $what;
        $this->_params = $params;
    }

    public function __toString() {
        return $this->_what . "(" . implode(", ", $this->_params) . ")";
    }

}