<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Proxy;

/**
 * Class ProxyGeneratorFactory
 * A factory for creating proxy generators.
 *
 * @package MooDev\Bounce\Proxy
 */
class ProxyGeneratorFactory {

    private $_proxyStore;

    /**
     * @param ProxyStore $proxyStore Thing we can store proxies in.
     * @param string $proxyNamespace Namespace under which to create proxy classes.
     */
    public function __construct($proxyStore = null, $proxyNamespace = 'MooDev\Bounce\Proxy\Generated'){
        if (is_string($proxyStore)) {
            // Urgh, deprecated usage
            trigger_error("Passing a temp dir to the ProxyGeneratorFactory is deprecated", E_USER_DEPRECATED);
            $proxyStore = new Store\FilesProxyStore($proxyStore);
        }
        if ($proxyStore === null) {
            // Default behaviour
            $proxyStore = new Store\InMemoryProxyStore();
        }
        $this->_proxyStore = $proxyStore;
        $this->_proxyStore->setBaseProxyNamespace($proxyNamespace);
    }

    /**
     * Instantiate a new LookupMethodProxyGenerator with the ProxyGeneratorFactory's config.
     * @param string $uniqueId a unique ID to use within the generator. e.g. Bean context uniqueId.
     * @return LookupMethodProxyGenerator
     */
    public function getLookupMethodProxyGenerator($uniqueId) {
        return new LookupMethodProxyGenerator($this->_proxyStore, $uniqueId);
    }

}