<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;

/**
 * Provides a simple value back. This is used for simple types
 * such as string, int, float, and boolean
 *
 * @author steves
 */
class SimpleValueProvider extends NoUndeclaredProperties implements ValueProvider
{

    /**
     * @var mixed the value to be returned by this provider
     */
    private $_value;

    public function __construct($value)
    {
        $this->_value = $value;
    }

    public function getValue(IBeanFactory $beanFactory)
    {
        return $this->_value;
    }
}
