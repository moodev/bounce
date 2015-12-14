<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Symfony;

use MooDev\Bounce\Context\ApplicationContext;
use MooDev\Bounce\Proxy\ProxyGeneratorFactory;
use MooDev\Bounce\Proxy\Store\FilesProxyStore;
use MooDev\Bounce\Proxy\Store\InMemoryProxyStore;
use MooDev\Bounce\Proxy\Utils\Base32Hex;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

/**
 * ApplicationContext that uses Symfony's DI framework and Bounce's config loader.
 */
class SymfonyApplicationContext extends ApplicationContext
{

    /**
     * @return ContainerInterface
     */
    protected function getContainerBuilderCached($contextFile, $cacheDir, $isDebug, $loaderFactory)
    {
        $contextFile = realpath($contextFile);

        $file = $cacheDir . '/' . basename($contextFile) . '.cache';
        $className = "c" . Base32Hex::encode($contextFile);

        $containerConfigCache = new ConfigCache($file, $isDebug);

        if (!$containerConfigCache->isFresh()) {
            $containerBuilder = $this->buildContainer($contextFile, $loaderFactory);

            $dumper = new PhpDumper($containerBuilder);
            $containerConfigCache->write(
                $dumper->dump(array('class' => $className)),
                $containerBuilder->getResources()
            );
        }

        require_once $file;
        return new $className();
    }

    /**
     * SymfonyApplicationContext constructor.
     * Note, Bounce's import code currently will NOT use Symfony's config loaders, it will use a bounce internal file
     * loader. Non bounce configs cannot be imported from a bounce context.
     * However, by passing a different LoaderFactory to this constructor you can load something that isn't a bounce
     * context into a new container wrapped to look like bounce.... potentially that config could include a bounce
     * context itself... Though your Loader will need to deal with both Symfony's XMLFileLoader and the BounceFileLoader
     * claiming the .xml file extension!
     *
     * @param string $contextFile Bounce context to load
     * @param string[] $customNamespaces Map of namespace names to ValueProvider of any custom namespaces.
     * @param string $cacheDir Directory that cached proxies and the compiled context should be loaded into. If this is
     * null, you will have no caching. Compilation will be required every single time this is constructed, and be slow.
     * @param bool $isDebug If true, check that the compiled context cache isn't stale every request.
     * @param LoaderFactory $loaderFactory Alternative Symfony config loader to use to load $contextFile.
     */
    public function __construct($contextFile, array $customNamespaces = [], $cacheDir = null, $isDebug = false, LoaderFactory $loaderFactory = null)
    {
        if ($loaderFactory === null) {
            if ($cacheDir) {
                $proxyStore = new FilesProxyStore($cacheDir . DIRECTORY_SEPARATOR . 'proxies');
            } else {
                $proxyStore = new InMemoryProxyStore();
            }
            $proxyGeneratorFactory = new ProxyGeneratorFactory($proxyStore);
            $loaderFactory = new DefaultLoaderFactory($customNamespaces, $proxyGeneratorFactory);
        }

        if ($cacheDir) {
            $containerBuilder = $this->getContainerBuilderCached($contextFile, $cacheDir, $isDebug, $loaderFactory);
        } else {
            $containerBuilder = $this->buildContainer($contextFile, $loaderFactory);
        }

        parent::__construct(new SymfonyContainerBeanFactory($containerBuilder));
    }

    protected function buildContainer($contextFile, LoaderFactory $loaderFactory) {
        $containerBuilder = new ContainerBuilder();

        $loader = $loaderFactory->getLoader($containerBuilder);
        $loader->load($contextFile);

        $containerBuilder->compile();
        return $containerBuilder;
    }

}