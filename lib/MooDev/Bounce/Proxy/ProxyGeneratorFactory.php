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

    private $_proxyNamespace;
    private $_tempDir;

    /**
     * @param string $tempDir Temp directory to put proxies in. Default is to create a new directory.
     * @param string $proxyNamespace Namespace under which to create proxy classes.
     */
    public function __construct($tempDir = null, $proxyNamespace = 'MooDev\Bounce\Proxy\Generated'){
        if (!isset($tempDir)) {
            // OK, we'll create ourselves a unique temp dir.
            do {
                $path = tempnam(sys_get_temp_dir(), 'bounce_');
                unlink($path);
            } while (!@mkdir($path)); // If this failed, we lost a race. Try again.
            $tempDir = $path;
            $proxyNamespace .= '\\' . strtr(basename($tempDir), '.', '_');
        }
        $this->_tempDir = $tempDir;
        $this->_proxyNamespace = $proxyNamespace;
    }

    /**
     * Instantiate a new LookupMethodProxyGenerator with the ProxyGeneratorFactory's config.
     * @param string $uniqueId a unique ID to use within the generator. e.g. Bean context uniqueId.
     * @return LookupMethodProxyGenerator
     */
    public function getLookupMethodProxyGenerator($uniqueId) {

        return new LookupMethodProxyGenerator($this->_tempDir, $this->_proxyNamespace, $uniqueId);

    }

}