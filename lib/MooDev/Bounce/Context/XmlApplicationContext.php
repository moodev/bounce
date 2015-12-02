<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Context;

use MooDev\Bounce\Exception\BounceException;
use MooDev\Bounce\Config;
use SimpleXMLElement;
use MooDev\Bounce\Xml\TypeSafeParser;

class XmlApplicationContext extends ApplicationContext
{

    /**
     * @param $xmlFilePath
     * @param bool $shareBeanCache whether to share the bean cache when the context's uniqueID (file path) is the same.
     * @param ValueTagProvider[] $customNamespaces
     * @throws \MooDev\Bounce\Exception\BounceException
     */
    public function __construct($xmlFilePath, $shareBeanCache = true, $customNamespaces = array())
    {
        $contextConfig = (new XmlContextParser($xmlFilePath, $customNamespaces))->getContext();
        //Create the bean factory
        parent::__construct(BeanFactory::getInstance($contextConfig, $shareBeanCache));
    }

}
