<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2013, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\Store;

/**
 * Class FilesProxyStore
 * Proxy store which stores proxies in a named directory.
 * This allows reuse of proxy classes between requests if you use the same directory.
 * @package MooDev\Bounce\Proxy
 */
class FilesProxyStore extends FilesystemProxyStore {

    /**
     * @param string $proxyDir Directory into which we should put proxies. Will be created if doesn't exist.
     */
    function __construct($proxyDir)
    {
        $this->_proxyDir = $proxyDir;
    }

    protected function _getProxyDir()
    {
        @mkdir($this->_proxyDir, 0777, true);
        return $this->_proxyDir;
    }

}