<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

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

    public function getValue(BeanFactory $beanFactory)
    {
        return $this->_value;
    }
}
