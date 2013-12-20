<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2013, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Proxy;

/**
 * Interface ProxyStore
 * Something that can save and load proxy classes.
 * @package MooDev\Bounce\Proxy
 */
interface ProxyStore {

    /**
     * Import the class for the given ID and name, if it was last modified at or after $lastModified.
     * @param string $uniqueId unique ID used by the bean factory
     * @param string $name proxy name
     * @param int $lastModified timestamp we expect the stored bean to be modified at or after.
     * @return true if we think we loaded the proxy successfully, false if we failed or it had expired.
     */
    public function import($uniqueId, $name, $lastModified);

    /**
     * Store some code for the given ID and name, and import it.
     * @param string $uniqueId unique ID used by the bean factory
     * @param string $name proxy name
     * @param string $code PHP code to store and import.
     * @param int $lastModified timestamp we expect the stored bean to be modified at or after.
     * @return true if we think we stored and then loaded the proxy successfully, false if we failed.
     */
    public function storeAndImport($uniqueId, $name, $code, $lastModified);

    /**
     * Set the base namespace for proxies we store.
     * @param string $proxyNamespace The base namespace.
     * @return null
     */
    public function setBaseProxyNamespace($proxyNamespace);

    /**
     * Obtain the proxy namespace that ought to be used for classes in this store.
     * @return string The actual proxy namespace to use.
     */
    public function getProxyNamespace();

}