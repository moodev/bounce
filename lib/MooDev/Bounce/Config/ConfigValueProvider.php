<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

/**
 * Provides a value from the global config object on the Zend_Registry
 *
 * @author steves
 */
class ConfigValueProvider extends NoUndeclaredProperties implements ValueProvider
{
    private $_configPath;

    public function __construct($configPath)
    {
        $this->_configPath = $configPath;
    }

    /**
     * Returns the value of the defined config name
     */
    public function getValue(BeanFactory $beanFactory)
    {
        $config = \Zend_Registry::get("config");
        $configPathElements = explode(".", $this->_configPath);
        $value = $config;
        foreach ($configPathElements as $configPathElement) {
            if (!isset($value->$configPathElement)) {
                return null;
            }
            $value = $value->$configPathElement;
        }
        return $value;
    }
}
