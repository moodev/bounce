<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Symfony;


use MooDev\Bounce\Config\Configurable;

/**
 * Symfony Configurator for configuring Bounce Configurables.
 *
 * Internal implementation detail of the SymfonyConfigBeanFactory.
 */
class SymfonyConfigurator
{

    /**
     * @param object $thing A newly instantiated thing to configure.
     */
    public function configure($thing)
    {
        // We will get called for non-Configurables due to it being impossible to work out whether or not things will
        // actually implement this interface at context compilation time. The bean factory falls back to using a
        // configurable if it isn't definitely sure what the class of the instantiated bean will be.
        if ($thing instanceof Configurable) {
            $thing->configure();
        }
    }

}