<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;

/**
 * Returns null as the value for a configuration item
 *
 * @author steves
 */
class NullValueProvider implements ValueProvider
{
    public function getValue(IBeanFactory $beanFactory)
    {
        return null;
    }
}
