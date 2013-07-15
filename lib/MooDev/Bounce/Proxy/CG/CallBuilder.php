<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\CG;

/**
 * Class CallBuilder
 * A builder for generating Call objects.
 * @package MooDev\Bounce\Proxy\CG
 */
class CallBuilder {

    /**
     * @var Param[]
     */
    private $_params = array();

    /**
     * @var string
     */
    private $_what;

    /**
     * @param string $what What we're calling. A function name, method call, whatever.
     */
    public function __construct($what) {
        $this->_what = $what;
    }

    /**
     * Get a CallBuilder instance.
     * @param string $what What we're calling. A function name, method call, whatever.
     * @return CallBuilder
     */
    public static function build($what) {
        return new CallBuilder($what);
    }

    /**
     * @param string $name A parameter to pass into the call.
     * @return CallBuilder
     */
    public function addParam($name) {
        $this->_params[] = $name;
        return $this;
    }

    /**
     * @return Call The call object that this builder has created.
     */
    public function getCall() {
        return new Call($this->_what, $this->_params);
    }

}