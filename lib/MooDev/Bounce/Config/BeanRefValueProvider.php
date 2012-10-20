<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

/**
 * Priovides a value using a bean name, and returning the bean with that name
 * from the provided BeanFactory instance
 *
 * @author steves
 */
class BeanRefValueProvider extends NoUndeclaredProperties implements ValueProvider
{

    /**
     * @var string the bean reference name we should retrieve from the context via
     * the bean factory
     */
    private $_beanName;

    public function __construct($beanName)
    {
        $this->_beanName = $beanName;
    }

    public function getValue(BeanFactory $beanFactory)
    {
        return $beanFactory->createByName($this->_beanName);
    }
}
