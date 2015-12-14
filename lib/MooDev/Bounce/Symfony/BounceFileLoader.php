<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Symfony;


use MooDev\Bounce\Config\Context;
use MooDev\Bounce\Context\XmlContextParser;
use MooDev\Bounce\Proxy\ProxyGeneratorFactory;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * A Symfony Config FileLoader that will load a Bounce xml config into a Symfony DI container.
 */
class BounceFileLoader extends FileLoader
{

    /**
     * @var string[]
     */
    private $customNamespaces;

    /**
     * @var ProxyGeneratorFactory
     */
    private $proxyGeneratorFactory;

    /**
     * BounceFileLoader constructor.
     * @param ContainerBuilder $container Container to load the config into.
     * @param FileLocatorInterface $locator Locator that'll be used to lookup files.
     * @param ProxyGeneratorFactory $proxyGeneratorFactory A proxy generator for lookup methods
     * @param string[] $customNamespaces Map of custom namespace names to ValueProviders for that namespace.
     */
    public function __construct(ContainerBuilder $container, FileLocatorInterface $locator, ProxyGeneratorFactory $proxyGeneratorFactory = null, $customNamespaces = [])
    {
        parent::__construct($container, $locator);
        $this->proxyGeneratorFactory = $proxyGeneratorFactory;
        $this->customNamespaces = $customNamespaces;
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string|null $type The resource type or null if unknown
     *
     * @throws \Exception If something went wrong
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        $bounceParser = new XmlContextParser($path, $this->customNamespaces);
        $bounceContext = $bounceParser->getContext();

        $this->importContext($bounceContext);


    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'xml' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    private function registerConfigurator()
    {
        $name = "bounceConfigurableConfigurator";
        if (!$this->container->has($name)) {
            $this->container->setDefinition($name, new Definition('MooDev\Bounce\Symfony\SymfonyConfigurator'));
        }
        return [new Reference($name), 'configure'];
    }

    protected function importContext(Context $context) {
        $lookupProxyGenerator = null;
        if ($this->proxyGeneratorFactory) {
            $lookupProxyGenerator = $this->proxyGeneratorFactory->getLookupMethodProxyGenerator($context->uniqueId);
        }

        $configurator = $this->registerConfigurator();

        $configBeanFactory = new SymfonyConfigBeanFactory($configurator, $lookupProxyGenerator);
        foreach ($context->childContexts as $childContext) {
            $this->importContext($childContext);
        }
        foreach ($context->beans as $bean) {
            if (empty($bean->name)) {
                // TODO: wat.
                continue;
            }
            $this->container->setDefinition($bean->name, $configBeanFactory->create($bean));
        }
        $this->container->addResource(new FileResource($context->fileName));
    }

}