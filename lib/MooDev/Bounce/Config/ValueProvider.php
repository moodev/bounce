<?php

namespace MooDev\Bounce\Config;

use MooDev\Bounce\Context\BeanFactory;

interface ValueProvider
{
    /**
     * Returns the value for the property from the configured provider object
     *
     * @param $beanFactory BeanFactory a bean factory
     *                     instance that can be used to create nested beans.
     */
    public function getValue(BeanFactory $beanFactory);
}
