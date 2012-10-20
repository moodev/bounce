<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Config;

use MooDev\Bounce\Context\BeanFactory;

require_once __DIR__ . '/../../../TestInit.php';

/**
 * BeanRefValueProvider test case.
 */
class BeanRefValueProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testBeanProvider()
    {
        $parentBean = new Bean();
        $parentBean->name = "parent";
        $parentBean->class = "StdClass";
        $childBean = new Bean();
        $childBean->class = "StdClass";
        $childBean->properties["grandChild"] = new SimpleValueProvider("someValue");
        $parentBean->properties["child"] = new BeanRefValueProvider("child");

        $context = new Context();
        $context->beans["parent"] = $parentBean;
        $context->beans["child"] = $childBean;

        $beanFactory = BeanFactory::getInstance($context);

        $o = $beanFactory->createByName("parent");
        $this->assertEquals("someValue", $o->child->grandChild);
    }
}

