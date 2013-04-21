<?php


namespace MooDev\Bounce\Proxy;


class ProxyGeneratorFactory {

    private $_proxyNamespace;
    private $_tempDir;

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

    public function getLookupMethodProxyGenerator($uniqueId) {

        return new LookupMethodProxyGenerator($this->_tempDir, $this->_proxyNamespace, $uniqueId);

    }

}