<?php

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

/**
 * Returns null as the value for a configuration item
 *
 * @author steves
 */
class NullValueProvider implements ValueProvider
{
    public function getValue(BeanFactory $beanFactory)
    {
        return null;
    }
}
