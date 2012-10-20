<?php

namespace MooDev\Bounce\Config;

/**
 * Root element which holds the configuration for a number of beans
 *
 * @author steves
 */
class Context extends NoUndeclaredProperties
{

    /**
     * @var string a unique identifier for this context configuration. This will be used to
     * reuse the same BeanFactory instance when creating the application context if context
     * sharing is enabled.
     */
    public $uniqueId = null;
    
    /**
     * @var Bean[] keyed on the bean name that are directly defined
     * on this context.
     */
    public $beans = array();

    /**
     * @var Context[] list of child contexts which are referenced or
     * imported by this context. This will be queried if the BeanFactory for this context requests
     * a bean which is defined on an imported context.
     */
    public $childContexts = array();
    
}
