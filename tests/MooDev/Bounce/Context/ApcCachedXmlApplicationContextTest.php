<?php
namespace MooDev\Bounce\Context;
require_once __DIR__ . '/../../../TestInit.php';

/**
 * ApcCachedXmlApplicationContext test case.
 */
class ApcCachedXmlApplicationContextTest extends \PHPUnit_Framework_TestCase
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
        $xmlContext = new ApcCachedXmlApplicationContext($xmlFile);
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

    public function testLoadFullXmlTwice()
    {
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        if (!defined("SIMPLE_CONSTANT")) {
            define("SIMPLE_CONSTANT", "Hello!");
        }
        $xmlFile = __DIR__ . "/fullContext.xml";
        $xmlContext = new ApcCachedXmlApplicationContext($xmlFile);
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

        $xmlContext = new ApcCachedXmlApplicationContext($xmlFile);
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
}

