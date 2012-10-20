<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Context;
use MooDev\Bounce\Exception\BounceException;

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

    public function testObviousImportLoop()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/selfImporting.xml";
        try {
            new XmlApplicationContext($xmlFile);
            $this->fail("No exception for infinite loop");
        } catch (BounceException $e) {
            $this->assertEquals(0, strpos($e->getMessage(), "Infinite recursion import detected"));
        }
    }

    public function testTransitiveImportLoop()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/loopParent.xml";
        try {
            new XmlApplicationContext($xmlFile);
            $this->fail("No exception for infinite loop");
        } catch (BounceException $e) {
            $this->assertEquals(0, strpos($e->getMessage(), "Infinite recursion import detected"));
        }
    }
}

