<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;

class LookupMethod extends NoUndeclaredProperties
{

    public function __construct($name, $bean) {
        $this->name = $name;
        $this->bean = $bean;
    }

    public $name;

    public $bean;

}
