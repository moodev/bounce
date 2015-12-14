<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;
use MooDev\Bounce\Exception\BounceException;

/**
 * Provides a file path relative to path specified by the real parent of a directory
 * defined by DOC_DIR. DOC_DIR is typically the root "/" directory of the vhost, and
 * we check that the path defined here is relative to the immediate parent of that dir
 *
 * Note that this has a security check within it to ensure that only paths within
 * the real parent of DOC_DIR are accessible.
 *
 * @author steves
 */
class FilePathValueProvider extends NoUndeclaredProperties implements ValueProvider
{

    private $_relativePath;
    protected $_rootDirectory;

    public function __construct($relativePath)
    {
        $this->_relativePath = $relativePath;
        $this->_rootDirectory = realpath(realpath(constant("DOC_DIR")) . "/../");
    }

    public function getValue(IBeanFactory $beanFactory)
    {
        $filePath = $this->_joinPaths($this->_rootDirectory, $this->_relativePath);
        //Now turn this into a canonical file path so that we can check it's
        //really contained within the defined root for this resolver
        $canonicalPath = realpath($filePath);
        //If the canonicalPath is false, then the path doesn't exist, so we will
        //let this through.
        if ($canonicalPath !== false && !(strpos($canonicalPath, realpath($this->_rootDirectory)) === 0)) {
            throw new BounceException("Relative path $this->_relativePath ($canonicalPath) "
                . "does not lie within the root $this->_rootDirectory (" . realpath($this->_rootDirectory) . ")");
        }
        return $filePath;
    }

    /**
     * Safely joins a base path with a relative path
     *
     * @param string $base         the base directory path, with or without trailing slash
     * @param string $relativePath the relative path to be added to the base path
     * @return string the combined filepath
     */
    protected function _joinPaths($base, $relativePath)
    {
        $paths = array(rtrim($base, '/'), trim($relativePath, '/'));
        return join('/', $paths);
    }
}
