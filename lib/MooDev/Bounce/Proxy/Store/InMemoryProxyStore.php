<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2013, MOO Print Ltd.
 * @license ISC
 */


namespace MooDev\Bounce\Proxy\Store;
use MooDev\Bounce\Proxy\ProxyStore;

/**
 * Class InMemoryProxyStore
 * Proxy store that doesn't actually bother storing stuff, it just loads the code and assumes that's good enuf.
 * This will not persist generated proxies across requests.
 * @package MooDev\Bounce\Proxy
 */
class InMemoryProxyStore implements ProxyStore {

    private $_proxyNamespace;

    public function import($uniqueId, $name, $lastModified)
    {
        return true;
    }

    public function storeAndImport($uniqueId, $name, $code, $lastModified)
    {
        if (eval($code) === false) {
            return false;
        }
        return true;
    }

    public function setBaseProxyNamespace($proxyNamespace)
    {
        $this->_proxyNamespace = $proxyNamespace . '\H' . spl_object_hash($this);
    }

    public function getProxyNamespace()
    {
        return $this->_proxyNamespace;
    }
}