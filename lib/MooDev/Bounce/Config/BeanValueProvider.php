<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;

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

    public function getValue(IBeanFactory $beanFactory)
    {
        return $beanFactory->create($this->_bean);
    }
}
