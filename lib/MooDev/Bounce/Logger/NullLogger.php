<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Logger;

class NullLogger implements Logger
{


    public function info($msg)
    {
    }

    public function debug($msg)
    {
    }

    public function emerg($msg)
    {
    }

    public function warn($msg)
    {
    }
}
