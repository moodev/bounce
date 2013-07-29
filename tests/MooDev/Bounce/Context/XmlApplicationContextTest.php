<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Context;
use MooDev\Bounce\Exception\BounceException;
use SimpleXMLElement;
use MooDev\Bounce\Config;

require_once __DIR__ . '/../../../TestInit.php';

/**
 * XmlApplicationContext test case.
 */
class XmlApplicationContextTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadFullXml()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/fullContext.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $this->assertNotNull($xmlContext->get("one"));
        $one = $xmlContext->get("one");
        $this->assertEquals("Hello!", $one->const);
        $this->assertEquals("simpleString", $one->simpleString);
        $this->assertEquals(2, $one->simpleInt);
        $this->assertEquals(2.3, $one->simpleFloat);
        $this->assertEquals("implicitString", $one->implicitString);
        $this->assertEquals(4, $one->implicitInt);
        $this->assertEquals(3.2, $one->implicitFloat);
        $this->assertEquals("explicitString", $one->explicitString);
        $this->assertEquals(4, $one->explicitInt);
        $this->assertEquals(3.2, $one->explicitFloat);
        $this->assertTrue($one->explicitBool);
    }

    public function testNestedBean()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/fullContext.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $one = $xmlContext->get("one");
        //Now look at the parent bean
        $two = $xmlContext->get("two");
        $this->assertEquals($one, $two->childByRef);
        $this->assertEquals("test!", $two->childByDefinition->testNestedBeanProp);
    }

    public function testFileObject()
    {
        $rootDir = realpath(__DIR__ . '/../../../../');
        if (!defined("ROOT_DIR")) {
            define("ROOT_DIR", $rootDir);
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/fullContext.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $fileReader = $xmlContext->get("fileReader");
        $this->assertEquals($rootDir . "/build.xml", $fileReader->file);
    }

    public function testNullObject()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/fullContext.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $nullObj = $xmlContext->get("nullType");
        $this->assertNull($nullObj->prop);
    }

    public function testMapObject()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/fullContext.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $mapBean = $xmlContext->get("mapBean");
        $this->assertEquals("steve", $mapBean->map["one"]);
        $this->assertEquals("simon", $mapBean->map["two"]);
    }

    public function testListObject()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/fullContext.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $listBean = $xmlContext->get("listBean");
        $this->assertEquals("steve", $listBean->list[0]);
        $this->assertEquals("simon", $listBean->list[1]);
        $this->assertEquals($xmlContext->get("fileReader"), $listBean->list[2]);
    }

    public function testImportChild()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/parent.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $parentBean = $xmlContext->get("parentBean");
        $this->assertEquals("simpleString", $parentBean->child->simpleString);
    }

    public function testGetBeanClasses()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/parent.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $this->assertEquals(array("childBean" => "StdClass", "parentBean" => "StdClass"), $xmlContext->getAllBeanClasses());

    }

    public function testImportChain()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/grandparent.xml";
        $xmlContext = new XmlApplicationContext($xmlFile);
        $grandParentBean = $xmlContext->get("grandParentBean");
        $this->assertEquals("simpleString", $grandParentBean->grandchild->simpleString);
    }

    /**
     * @expectedException \MooDev\Bounce\Exception\BounceException
     * @expectedExceptionMessage Infinite recursion import detected
     */
    public function testObviousImportLoop()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/selfImporting.xml";
        new XmlApplicationContext($xmlFile);
    }

    /**
     * @expectedException \MooDev\Bounce\Exception\BounceException
     * @expectedExceptionMessage Infinite recursion import detected
     */
    public function testTransitiveImportLoop()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/loopParent.xml";
        new XmlApplicationContext($xmlFile);
    }

    public function testCustomNS() {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/customContext.xml";
        $xmlContext = new XmlApplicationContext($xmlFile, true, array("http://www.moo.com/xsd/fictional-1.0" => new TestAdditionalProvider()));
        $customBean = $xmlContext->get("customBean");
        $this->assertEquals("Testing", $customBean->custom);
    }

    public function testSimpleScope() {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/scopes.xml";
        $xmlContext = new XmlApplicationContext($xmlFile, true);
        $beanOne = $xmlContext->get("one");
        $beanTwo = $xmlContext->get("one");
        $this->assertEquals("foo", $beanOne->hi);
        $this->assertEquals("foo", $beanTwo->hi);
        $this->assertNotSame($beanOne, $beanTwo);

        $beanOne = $xmlContext->get("two");
        $beanTwo = $xmlContext->get("two");
        $this->assertSame($beanOne, $beanTwo);
    }

    public function testProxyScope() {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/scopes.xml";
        $xmlContext = new XmlApplicationContext($xmlFile, true);
        $beanOne = $xmlContext->get("three");
        $beanTwo = $xmlContext->get("three");
        $this->assertSame($beanOne, $beanTwo);
        $this->assertEquals("foo", $beanOne->goats()->hi);
        $this->assertEquals("foo", $beanTwo->goats()->hi);
        $this->assertNotSame($beanOne->goats(), $beanTwo->goats());
        $this->assertNotSame($beanOne->goats(), $beanOne->goats());

    }
}

class TestAdditionalProvider implements ValueTagProvider {

    /**
     * @param SimpleXMLElement $element
     * @param Config\Context $contextConfig
     * @return Config\ValueProvider
     */
    public function getValueProvider(SimpleXMLElement $element, Config\Context $contextConfig)
    {

        $name = $element->getName();
        if ($name == "test") {
            return new Config\SimpleValueProvider($element);
        }
        return null;

    }
}

