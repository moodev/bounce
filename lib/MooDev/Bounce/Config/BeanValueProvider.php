<?php

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

/**
 * Class that instantiates another bean based on a nested definition
 *
 * @author steves
 */
class BeanValueProvider extends NoUndeclaredProperties implements ValueProvider
{

    /**
     * @var Bean the bean definition to be used to create the value
     */
    private $_bean;

    public function __construct(Bean $bean)
    {
        $this->_bean = $bean;
    }

    public function getValue(BeanFactory $beanFactory)
    {
        return $beanFactory->create($this->_bean);
    }
}
