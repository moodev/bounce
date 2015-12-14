<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;

/**
 * Provides a value from a global PHP constant
 *
 * @author steves
 */
class ConstantValueProvider extends NoUndeclaredProperties implements ValueProvider
{
    private $_constantName;

    public function __construct($constantName)
    {
        $this->_constantName = $constantName;
    }

    /**
     * Returns the value of the defined constant name
     */
    public function getValue(IBeanFactory $beanFactory)
    {
        return defined($this->_constantName) ? constant($this->_constantName) : null;
    }
}
