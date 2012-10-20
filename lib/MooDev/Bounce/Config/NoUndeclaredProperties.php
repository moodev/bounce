<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;

use \MooDev\Bounce\Exception\BounceException;

/**
 * Spew horrible errors at attempts to fiddle with undefined properties.
 * Good for detecting stupid typoes.
 */
class NoUndeclaredProperties
{
    function __get($name)
    {
        throw new BounceException("Attempt to access undeclared property: $name");
    }

    function __set($name, $value)
    {
        throw new BounceException("Attempt to set undeclared property: $name");
    }

    function __isset($name)
    {
        throw new BounceException("Attempt to isset undeclared property: $name");
    }

    function __unset($name)
    {
        throw new BounceException("Attempt to unset undeclared property: $name");
    }


}
