<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Context;

use SimpleXMLElement;
use \MooDev\Bounce\Config;

interface ValueTagProvider
{

    /**
     * @param SimpleXMLElement $element
     * @param Config\Context $contextConfig
     * @return Config\ValueProvider
     */
    public function getValueProvider(SimpleXMLElement $element, Config\Context $contextConfig);
}
