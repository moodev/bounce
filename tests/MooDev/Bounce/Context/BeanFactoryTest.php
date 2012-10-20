<?php
namespace MooDev\Bounce\Context;
require_once __DIR__ . '/../../../TestInit.php';

use MooDev\Bounce\Config;
use stdClass;
use MooDev\Bounce\Exception\BounceException;

/**
 * BeanFactory test case.
 */
class BeanFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigGuards()
    {
        $impl = BeanFactory::getInstance(new Config\Context());
        try {
            $impl->contextConfig = new BounceException();
        } catch (BounceException $e) {
            $this->assertEquals("Invalid contextConfig. Must be a Config\Context instance", $e->getMessage());
        }
        $impl->contextConfig = new Config\Context();
    }

    public function testEmptyConfig()
    {
        $impl = BeanFactory::getInstance(new Config\Context());
        try {
            $impl->createByName("bean");
        } catch (BounceException $e) {
            $this->assertEquals("No object defined with name bean", $e->getMessage());
        }
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
        } catch (BounceException $e) {
            $this->assertEquals("No object defined with name steve", $e->getMessage());
        }
    }
}

