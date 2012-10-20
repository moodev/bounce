<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Context;

/**
 * Acts as the entry point for retrieving fully-populated objects at
 * runtime using a pre-configured BeanFactory
 *
 * @author steves
 */
class ApplicationContext
{
    /**
     * @var $_beanFactory BeanFactory a bean factory instance
     * to retrieve objects from.
     */
    protected $_beanFactory;

    /**
     * Retrieves an element from the Context by name
     *
     * @param string $name the name of the object to be created from the Contex
     *                     configuration
     * @return mixed the populated object from the context
     */
    public function get($name)
    {
        return $this->_beanFactory->createByName($name);
    }
}
