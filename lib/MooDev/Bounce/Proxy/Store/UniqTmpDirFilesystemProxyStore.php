<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2013, MOO Print Ltd.
 * @license ISC
 */


namespace MooDev\Bounce\Proxy\Store;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class UniqTmpDirFilesystemProxyStore
 * A filesystem proxy store that creates a new temp dir for proxies every time it is instantiated.
 * This isn't very helpful since it doesn't allow reuse of proxies between requests.
 * This used to be the default behaviour.
 * @package MooDev\Bounce\Proxy
 */
class UniqTmpDirFilesystemProxyStore extends FilesystemProxyStore {

    protected $_tmpDir;

    protected $_proxyDir;

    /**
     * @param string $tmpDir The base temp directory to create our dirs in, or null for the system temp dir.
     */
    function __construct($tmpDir = null)
    {
        if ($tmpDir === null) {
            $tmpDir = sys_get_temp_dir();
        }
        $this->_tmpDir = $tmpDir;
    }


    protected function _getProxyDir()
    {
        if ($this->_proxyDir === null) {
            do {
                $path = tempnam($this->_tmpDir, 'bounce_');
                @unlink($path);
            } while (!@mkdir($path, 0777, true)); // If this failed, we lost a race. Try again.
            $this->_proxyDir = $path;
        }
        return $this->_proxyDir;
    }

    public function __destruct()
    {
        if ($this->_proxyDir !== null) {
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_proxyDir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                /**
                 * @var \SplFileInfo $path
                 */
                $path->isFile() ? @unlink($path->getPathname()) : @rmdir($path->getPathname());
            }
            @rmdir($this->_proxyDir);
            $this->_proxyDir = null;
        }
    }

}