<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\CG;

/**
 * Class Call
 * A function/method/whatever call.
 * @package MooDev\Bounce\Proxy\CG
 */
class Call {

    /**
     * @var string
     */
    private $_what;

    /**
     * @var Param[]
     */
    private $_params = array();

    /**
     * @param string $what What we're calling. A function name, method, whatever.
     * @param Param[] $params Array of params that we're being called with.
     */
    public function __construct($what, array $params = array()) {
        $this->_what = $what;
        $this->_params = $params;
    }

    public function __toString() {
        return $this->_what . "(" . implode(", ", $this->_params) . ")";
    }

}