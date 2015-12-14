<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;

use MooDev\Bounce\Context\IBeanFactory;

interface ValueProvider
{
    /**
     * Returns the value for the property from the configured provider object
     *
     * @param $beanFactory IBeanFactory a bean factory
     *                     instance that can be used to create nested beans.
     */
    public function getValue(IBeanFactory $beanFactory);
}
