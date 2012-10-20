<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Logger;

class NullLogFactory
{

    public static function getLog($class) {
        return new NullLogger();
    }

}
