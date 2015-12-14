<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;

/**
 * Value provider which takes a map of value providers and returns a map
 * of string => objects/values create from them when requested
 *
 * @author steves
 */
class MapValueProvider extends NoUndeclaredProperties implements ValueProvider
{

    /**
     * @var ValueProvider[] keyed by map index name - a map of
     * value providers to be used to create a list of values for use as a property of
     * constructor of another bean
     */
    private $_valueProvidersMap;

    public function __construct(array $valueProvidersMap)
    {
        $this->_valueProvidersMap = $valueProvidersMap;
    }

    public function getValue(IBeanFactory $beanFactory)
    {
        $outputMap = array();
        foreach ($this->_valueProvidersMap as $mapIndex => $valueProvider) {
            $outputMap[$mapIndex] = $valueProvider->getValue($beanFactory);
        }
        return $outputMap;
    }
}
