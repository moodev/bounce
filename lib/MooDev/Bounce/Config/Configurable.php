<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;

/**
 * Bounce will call the configure method after instantiating any class that implements this interface.
 */
interface Configurable
{

    public function configure();

}
