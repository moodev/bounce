<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\IBeanFactory;

/**
 * Value provider which takes a list of value providers and returns a list
 * of objects/values create from them when requested
 *
 * @author steves
 */
class ListValueProvider extends NoUndeclaredProperties implements ValueProvider
{

    /**
     * @var ValueProvider[] a list of value providers to be used to
     * create a list of values for use as a property of constructor of another bean
     */
    private $_valueProviders;

    public function __construct(array $valueProviders)
    {
        $this->_valueProviders = $valueProviders;
    }

    public function getValue(IBeanFactory $beanFactory)
    {
        $outputArray = array();
        foreach ($this->_valueProviders as $valueProvider) {
            $outputArray[] = $valueProvider->getValue($beanFactory);
        }
        return $outputArray;
    }
}
