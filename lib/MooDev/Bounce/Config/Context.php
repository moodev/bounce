<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

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
     * reuse the same IBeanFactory instance when creating the application context if context
     * sharing is enabled.
     */
    public $uniqueId = null;

    /**
     * @var string Path to the file that the context was loaded from.
     */
    public $fileName = null;
    
    /**
     * @var Bean[] keyed on the bean name that are directly defined
     * on this context.
     */
    public $beans = array();

    /**
     * @var Context[] list of child contexts which are referenced or
     * imported by this context. This will be queried if the IBeanFactory for this context requests
     * a bean which is defined on an imported context.
     */
    public $childContexts = array();
    
}
