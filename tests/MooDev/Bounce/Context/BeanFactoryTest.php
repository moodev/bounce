<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Context;
require_once __DIR__ . '/../../../TestInit.php';

use MooDev\Bounce\Config;
use MooDev\Bounce\Proxy\ProxyGeneratorFactory;
use stdClass;
use MooDev\Bounce\Exception\BounceException;

/**
 * BeanFactory test case.
 */
class BeanFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        parent::setUp();
        BeanFactory::$proxyGeneratorFactory = new ProxyGeneratorFactory();
    }

    /**
     * @expectedException \MooDev\Bounce\Exception\BounceException
     * @expectedExceptionMessage Invalid contextConfig. Must be a Config\Context instance
     */
    public function testConfigGuards()
    {
        $impl = BeanFactory::getInstance(new Config\Context());
        $impl->contextConfig = new BounceException();
    }

    /**
     * @expectedException \MooDev\Bounce\Exception\BounceException
     * @expectedExceptionMessage No object defined with name bean
     */
    public function testEmptyConfig()
    {
        $impl = BeanFactory::getInstance(new Config\Context());
        $impl->createByName("bean");
    }

    public function testSimpleInstantiate()
    {
        $impl = BeanFactory::getInstance(new Config\Context());
        $bean = new Config\Bean();
        $bean->class = "StdClass";
        $o = $impl->create($bean);
        $this->assertTrue($o instanceof StdClass);
    }

    public function testSimpleInstantiateWithProperties()
    {
        $impl = BeanFactory::getInstance(new Config\Context());
        $bean = new Config\Bean();
        $bean->class = "StdClass";
        $o = $impl->create($bean);
        $this->assertTrue($o instanceof StdClass);
        $this->assertFalse(isset($o->test));
        $bean->properties["test"] = new Config\SimpleValueProvider("steve");
        $o2 = $impl->create($bean);
        $this->assertEquals("steve", $o2->test);
    }

    public function testCreateByName()
    {
        $parentBean = new Config\Bean();
        $parentBean->name = "parent";
        $parentBean->class = "StdClass";
        $childBean = new Config\Bean();
        $childBean->class = "StdClass";
        $childBean->properties["grandChild"] = new Config\SimpleValueProvider("someValue");
        $parentBean->properties["child"] = new Config\BeanValueProvider($childBean);

        $config = new Config\Context();
        $config->beans["parent"] = $parentBean;

        $beanFactory = BeanFactory::getInstance($config);
        $o = $beanFactory->createByName("parent");
        $this->assertEquals("someValue", $o->child->grandChild);
        $o2 = $beanFactory->createByName("parent");
        $this->assertEquals($o, $o2);

        try {
            $beanFactory->createByName("steve");
            $this->fail("Was able to create bean by non-existent name");
        } catch (BounceException $e) {
            $this->assertEquals("No object defined with name steve", $e->getMessage());
        }
    }

    public function testCreateByNameWithContextUniqueId()
    {
        $parentBean = new Config\Bean();
        $parentBean->name = "parent";
        $parentBean->class = "StdClass";
        $childBean = new Config\Bean();
        $childBean->class = "StdClass";
        $childBean->properties["grandChild"] = new Config\SimpleValueProvider("someValue");
        $parentBean->properties["child"] = new Config\BeanValueProvider($childBean);

        $config = new Config\Context();
        $config->beans["parent"] = $parentBean;
        $config->uniqueId = "Steve";

        $beanFactory = BeanFactory::getInstance($config);
        $o = $beanFactory->createByName("parent");
        $this->assertEquals("someValue", $o->child->grandChild);
        $o2 = $beanFactory->createByName("parent");
        $this->assertEquals($o, $o2);

        try {
            $beanFactory->createByName("steve");
            $this->fail("Was able to create bean by non-existent name");
        } catch (BounceException $e) {
            $this->assertEquals("No object defined with name steve", $e->getMessage());
        }
    }

    public function testMethodInjection()
    {
        $context = new Config\Context();
        $bean = new Config\Bean();
        $bean->class = "StdClass";
        $bean->name = "in";
        $bean->properties["test"] = new Config\SimpleValueProvider("jon");
        $context->beans["in"] = $bean;

        $bean = new Config\Bean();
        $bean->class = "StdClass";
        $bean->name = "out";
        $context->beans["out"] = $bean;
        $bean->lookupMethods = array(new Config\LookupMethod("getInner", "in"));

        $impl = BeanFactory::getInstance($context);
        $o = $impl->createByName("out");
        $this->assertEquals("jon", $o->getInner()->test);

    }

    public function testMethodInjectionConstructors()
    {
        $context = new Config\Context();
        $bean = new Config\Bean();
        $bean->class = "StdClass";
        $bean->name = "in";
        $bean->properties["test"] = new Config\SimpleValueProvider("jon");
        $context->beans["in"] = $bean;

        $bean = new Config\Bean();
        $bean->class = '\MooDev\Bounce\Context\methodInjectionTestClass';
        $bean->name = "out";
        $context->beans["out"] = $bean;
        $bean->lookupMethods = array(new Config\LookupMethod("getInner", "in"));
        $bean->constructorArguments = array(
                 new Config\SimpleValueProvider("hello"),
                 new Config\SimpleValueProvider("world")
            );


        $impl = BeanFactory::getInstance($context);
        $o = $impl->createByName("out");
        $this->assertEquals("jon", $o->getInner()->test);
        $this->assertEquals("hello", $o->a);
        $this->assertEquals("world", $o->b);


    }
}


class methodInjectionTestClass {

    public $a;
    public $b;

    public function __construct($a, $b) {
        $this->a = $a;
        $this->b = $b;
    }

}

