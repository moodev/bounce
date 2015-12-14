<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Symfony;


use MooDev\Bounce\Proxy\ProxyGeneratorFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A Loader Factory that loads files using the BounceFileLoader only.
 */
class DefaultLoaderFactory implements LoaderFactory
{

    private $customNamespaces;

    private $proxyGeneratorFactory;

    /**
     * DefaultLoaderFactory constructor.
     * @param string[] $customNamespaces
     * @param ProxyGeneratorFactory $proxyGeneratorFactory A proxy generator factory, or null to disable lookup methods.
     */
    public function __construct(array $customNamespaces = [], ProxyGeneratorFactory $proxyGeneratorFactory = null)
    {
        $this->proxyGeneratorFactory = $proxyGeneratorFactory;
        $this->customNamespaces = $customNamespaces;
    }


    /**
     * @param ContainerInterface $container
     * @return LoaderInterface
     */
    public function getLoader(ContainerInterface $container)
    {
        $fileLocator = new FileLocator();
        return new BounceFileLoader($container, $fileLocator, $this->proxyGeneratorFactory, $this->customNamespaces);
    }
}