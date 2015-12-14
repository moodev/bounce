<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Symfony;


use MooDev\Bounce\Config\Bean;
use MooDev\Bounce\Context\IBeanFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A BeanFactory that retrieves services from a Symfony container.
 */
class SymfonyContainerBeanFactory implements IBeanFactory
{

    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * Create/retrieve an instance of a named bean.
     * @param string $name
     * @return mixed
     */
    public function createByName($name)
    {
        return $this->container->get($name);
    }

    /**
     * @return string[] A map of bean names to class names.
     */
    public function getAllBeanClasses()
    {
        throw new \RuntimeException("Not implemented.");
    }

    /**
     * @param Bean $_bean
     * @return mixed
     */
    public function create(Bean $_bean)
    {
        throw new \RuntimeException("Not implemented.");
    }

}