<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Config;

use MooDev\Bounce\Context\BeanFactory;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../TestInit.php';

/**
 * BeanValueProvider test case.
 */
class BeanValueProviderTest extends TestCase
{

    public function testBeanProvider()
    {
        $parentBean = new Bean();
        $parentBean->name = "parent";
        $parentBean->class = "StdClass";
        $childBean = new Bean();
        $childBean->class = "StdClass";
        $childBean->properties["grandChild"] = new SimpleValueProvider("someValue");
        $parentBean->properties["child"] = new BeanValueProvider($childBean);

        $beanFactory = BeanFactory::getInstance(new Context());
        $o = $beanFactory->create($parentBean);
        $this->assertEquals("someValue", $o->child->grandChild);
    }
}

