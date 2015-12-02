<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Context;

use MooDev\Bounce\Config;
use MooDev\Bounce\Logger\Logger;

/**
 * Sub-class of the XmlApplicationContext which caches the parsed configuration
 * objects into the APC cache.
 *
 * It keeps track of both modification times, and the inodes of all the files
 * which have been read in to create the configuration. If any files are newer
 * or have different inodes, then reload everything and re-cache it.
 *
 * @author steve
 *
 */
class ApcCachedXmlApplicationContext extends ApplicationContext
{

    /**
     * @param $xmlFilePath
     * @param bool $shareBeanCache whether to share the bean cache when the context's uniqueID (file path) is the same.
     * @param ValueTagProvider[] $customNamespaces
     * @param array $logFactory
     */
    public function __construct($xmlFilePath, $shareBeanCache = true, $customNamespaces = array(), $logFactory = array('\MooDev\Bounce\Logger\NullLogFactory', 'getLog'))
    {
        $contextConfig = (new XmlContextParser($xmlFilePath, $customNamespaces))->getContext();
        //Create the bean factory
        parent::__construct(BeanFactory::getInstance($contextConfig, $shareBeanCache));
    }

}
