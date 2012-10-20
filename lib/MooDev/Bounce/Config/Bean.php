<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;

/**
 * Represents a Bean within the Context. Note that it may not be a
 * root item within the context as beans can be nested within properties
 * or arguments to other beans.
 *
 * The Bean holds the definition of how to create a single object, including
 * a definition of all the constructor arguments, and the properties that should
 * be set on the object before being returned by the ApplicationContext
 *
 * @author steves
 */
class Bean extends NoUndeclaredProperties
{
    /**
     * @var string the unique name for the bean within this context. Can be null
     * particularly for a nested bean
     */
    public $name = null;

    /**
     * @var string the name of the class for this bean
     */
    public $class;

    /**
     * @var ValueProvider[] keyed on the property name
     */
    public $properties = array();

    /**
     * @var ValueProvider[] in a list, in the order they
     * should be passed to the constructor
     */
    public $constructorArguments = array();

    /**
     * @var string Factory method to call. If this is set then rather than instantiating the bean directly, we'll call
     * a static method on the class with the given constructorArgs, and expect the result to be
     * our instance.
     */
    public $factoryMethod = null;

    /**
     * @var string Factory bean to use. If this is set and factoryMethod is also set then rather than calling a static
     * on our class, we'll call the factoryMethod on an instance of the named factoryBean.
     */
    public $factoryBean = null;

}
