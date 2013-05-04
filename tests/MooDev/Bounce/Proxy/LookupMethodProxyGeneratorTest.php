<?php
namespace MooDev\Bounce\Proxy;
use MooDev\Bounce\Config\Bean;
use MooDev\Bounce\Config\LookupMethod;
use Mockery as m;

require_once __DIR__ . '/../../../TestInit.php';


class LookupMethodProxyGeneratorTest extends \PHPUnit_Framework_TestCase {

    public function testProxyGenerationNoMethodsStdClass() {
        $factory = new ProxyGeneratorFactory();
        $generator = $factory->getLookupMethodProxyGenerator("LookupMethodProxyGeneratorTest");

        $bean = new Bean();
        $bean->class = "StdClass";
        $bean->name = "out";

        $class = $generator->loadProxy($bean);

        $rClass = new \ReflectionClass($class);

        $this->assertEquals(1, $rClass->getConstructor()->getNumberOfParameters());
        $this->assertEquals(1, count($rClass->getMethods()));
    }

    public function testProxyGenerationNoMethodsExistingConstructor() {
        $factory = new ProxyGeneratorFactory();
        $generator = $factory->getLookupMethodProxyGenerator("LookupMethodProxyGeneratorTest");

        $bean = new Bean();
        $bean->class = '\MooDev\Bounce\Proxy\ProxyTestClass';
        $bean->name = "out";

        $class = $generator->loadProxy($bean);

        $rClass = new \ReflectionClass($class);
        $constructor = $rClass->getConstructor();
        $this->assertEquals(4, $constructor->getNumberOfParameters());
        $this->assertEquals(2, $constructor->getNumberOfRequiredParameters());
        $this->assertEquals(2, count($rClass->getMethods()));
        $params = $constructor->getParameters();
        reset($params);
        each($params);
        /**
         * @var \ReflectionParameter $param
         */
        list($key, $param) = each($params);
        $this->assertEquals("a", $param->getName());
        list($key, $param) = each($params);
        $this->assertEquals("b", $param->getName());
        $this->assertEquals("foo", @$param->getDefaultValue());
        list($key, $param) = each($params);
        $this->assertEquals("cat", $param->getName());
        $this->assertEquals("harhar", @$param->getDefaultValue());

        $this->assertTrue($rClass->hasMethod("getA"));
        $this->assertTrue($rClass->hasProperty("a"));
        $this->assertTrue($rClass->hasProperty("b"));
        $this->assertTrue($rClass->hasProperty("c"));

        $mockBeanFactory = m::mock('\MooDev\Bounce\Context\BeanFactory');

        /**
         * @var ProxyTestClass $instance
         */
        $instance = $rClass->newInstance($mockBeanFactory, "z", "x", "k");
        $this->assertEquals("z", $instance->a);
        $this->assertEquals("x", $instance->b);
        $this->assertEquals("k", $instance->c);
        $this->assertEquals("z", $instance->getA());

    }

    public function testProxyGenerationMethodsExistingConstructor() {
        $factory = new ProxyGeneratorFactory();
        $generator = $factory->getLookupMethodProxyGenerator("LookupMethodProxyGeneratorTest");

        $bean = new Bean();
        $bean->class = '\MooDev\Bounce\Proxy\ProxyTestClass';
        $bean->name = "out";
        $bean->lookupMethods = array(
            new LookupMethod("getInner", "in"),
            new LookupMethod("getThingy", "thingy"),
        );

        $class = $generator->loadProxy($bean);

        $rClass = new \ReflectionClass($class);
        $constructor = $rClass->getConstructor();
        $this->assertEquals(4, $constructor->getNumberOfParameters());
        $this->assertEquals(2, $constructor->getNumberOfRequiredParameters());
        $this->assertEquals(4, count($rClass->getMethods()));
        $params = $constructor->getParameters();
        reset($params);
        each($params);
        /**
         * @var \ReflectionParameter $param
         */
        list($key, $param) = each($params);
        $this->assertEquals("a", $param->getName());
        list($key, $param) = each($params);
        $this->assertEquals("b", $param->getName());
        $this->assertEquals("foo", @$param->getDefaultValue());
        list($key, $param) = each($params);
        $this->assertEquals("cat", $param->getName());
        $this->assertEquals("harhar", @$param->getDefaultValue());

        $this->assertTrue($rClass->hasMethod("getA"));
        $this->assertTrue($rClass->hasProperty("a"));
        $this->assertTrue($rClass->hasProperty("b"));
        $this->assertTrue($rClass->hasProperty("c"));

        $this->assertTrue($rClass->hasMethod("getInner"));
        $this->assertEquals(0, $rClass->getMethod("getInner")->getNumberOfParameters());
        $this->assertTrue($rClass->hasMethod("getThingy"));
        $this->assertEquals(0, $rClass->getMethod("getThingy")->getNumberOfParameters());

        $mockBeanFactory = m::mock('\MooDev\Bounce\Context\BeanFactory')
            ->shouldReceive("createByName")->with("in")->andReturn("wheeee")
            ->shouldReceive("createByName")->with("thingy")->andReturn("wooooo")
            ->mock();

        /**
         * @var ProxyTestClass $instance
         */
        $instance = $rClass->newInstance($mockBeanFactory, "z", "x", "k");
        $this->assertEquals("z", $instance->a);
        $this->assertEquals("x", $instance->b);
        $this->assertEquals("k", $instance->c);
        $this->assertEquals("z", $instance->getA());

        $this->assertEquals("wheeee", $instance->getInner());
        $this->assertEquals("wooooo", $instance->getThingy());

    }
}

class ProxyTestClass {

    public $a;
    public $b;
    public $c;

    public function __construct($a, $b = "foo", $cat = "harhar") {
        $this->a = $a;
        $this->b = $b;
        $this->c = $cat;
    }

    public function getA() {
        return $this->a;
    }

}
