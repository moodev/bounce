<?php

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

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
    public function getValue(BeanFactory $beanFactory)
    {
        return defined($this->_constantName) ? constant($this->_constantName) : null;
    }
}
