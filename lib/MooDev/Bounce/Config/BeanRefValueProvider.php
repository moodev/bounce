<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;

/**
 * Priovides a value using a bean name, and returning the bean with that name
 * from the provided IBeanFactory instance
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

    public function getValue(IBeanFactory $beanFactory)
    {
        return $beanFactory->createByName($this->_beanName);
    }
}
