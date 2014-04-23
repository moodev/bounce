<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2013, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\Store;


use MooDev\Bounce\Exception\BounceException;
use MooDev\Bounce\Proxy\ProxyStore;
use MooDev\Bounce\Proxy\Utils\Base32Hex;

/**
 * Class FilesystemProxyStore
 * Abstract base class for proxy stores which store PHP classes in a file in a directory.
 * @package MooDev\Bounce\Proxy
 */
abstract class FilesystemProxyStore implements ProxyStore {

    protected $_baseProxyNamespace;

    protected $_proxyNamespace;

    protected $_loaded = array();

    abstract protected function _getProxyDir();

    protected function _nameToFilename($uniqueId, $proxyName)
    {
        return $this->_getProxyDir() . DIRECTORY_SEPARATOR . ($uniqueId !== null ? ($uniqueId  . DIRECTORY_SEPARATOR) : '') . $proxyName . '.php';
    }

    public function import($uniqueId, $name, $lastModified)
    {
        $file = $this->_nameToFilename($uniqueId, $name);
        if (isset($this->_loaded[$file])) {
            return true;
        }
        /** @noinspection PhpIncludeInspection */
        if ($lastModified > @filemtime($file) || !@include $file) {
            return false;
        }
        $this->_loaded[$file] = true;
        return true;
    }

    public function store($uniqueId, $name, $code, $lastModified)
    {
        $file = $this->_nameToFilename($uniqueId, $name);

        $baseDir = dirname($file);
        @mkdir($baseDir, 0777, true);

        $tmpFile = tempnam($baseDir, basename($file));
        if ($tmpFile === false) {
            throw new BounceException("Unable to write proxy temp file");
        }
        // File header
        $code = '<?php'."\n\n".$code;

        $wrote = file_put_contents($tmpFile, $code);
        if ($wrote != strlen($code)) {
            unlink($tmpFile);
            throw new BounceException("Unable to write proxy temp file. Wrote $wrote bytes but expected " . strlen($code));
        }
        @rename($tmpFile, $file);
        @touch($file, $lastModified);
        return $file;
    }

    public function storeAndImport($uniqueId, $name, $code, $lastModified)
    {
        $file = $this->store($uniqueId, $name, $code, $lastModified);
        /** @noinspection PhpIncludeInspection */
        require $file;
        $this->_loaded[$file] = true;
        return true;
    }

    public function setBaseProxyNamespace($proxyNamespace)
    {
        $this->_baseProxyNamespace = $proxyNamespace;
    }

    public function getProxyNamespace()
    {
        if ($this->_proxyNamespace === null) {
            $this->_proxyNamespace = ltrim($this->_baseProxyNamespace . '\C' . Base32Hex::encode($this->_getProxyDir()), '\\');
        }
        return $this->_proxyNamespace;
    }


}