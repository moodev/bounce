<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Symfony;


use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface for obtaining Symfony config loaders for a Container.
 */
interface LoaderFactory
{

    /**
     * @param ContainerInterface $container Container that'll be loaded into.
     * @return LoaderInterface
     */
    public function getLoader(ContainerInterface $container);

}