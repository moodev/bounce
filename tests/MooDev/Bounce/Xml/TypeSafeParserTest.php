<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Xml;
use MooDev\Bounce\Exception\ParserException;

require_once __DIR__ . '/../../../TestInit.php';

/**
 *  test case.
 */
class TypeSafeParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Object Under Test
     *
     * @var TypeSafeParser
     */
    private $_typeSafeParser;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_typeSafeParser = new TypeSafeParser("http://www.moo.com/xsd/template-1.0");
    }

    public function testParseInt()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyInt>1234</MyInt>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myint = $this->_typeSafeParser->parseInt($rootElement, "MyInt");
        $this->assertEquals(1234, $myint);
    }

    public function testParseIntMissingRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyInt>1234</MyInt>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        try {
            $this->_typeSafeParser->parseInt($rootElement, "MyIntMissing");
            $this->fail("No exception thrown for missing int tag");
        } catch (ParserException $e) {
            $this->assertEquals(0, strpos($e->getMessage(), "Required tag MyIntMissing not found within element"));
        }
    }

    public function testParseIntMissingNotRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyInt>1234</MyInt>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->assertNull($this->_typeSafeParser->parseInt($rootElement, "MyIntMissing", false));
    }

    public function testParseIntWithDecimal()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyInt>1234.56</MyInt>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myint = $this->_typeSafeParser->parseInt($rootElement, "MyInt");
        $this->assertEquals(1234, $myint);
    }

    public function testParseFloat()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyFloat>1234.56</MyFloat>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myint = $this->_typeSafeParser->parseFloat($rootElement, "MyFloat");
        $this->assertEquals(1234.56, $myint);
    }

    public function testParseFloatMissingRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyFloat>1234.56</MyFloat>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        try {
            $this->_typeSafeParser->parseInt($rootElement, "MyFloatMissing");
            $this->fail("No exception thrown for missing float tag");
        } catch (ParserException $e) {
            $this->assertEquals(0, strpos($e->getMessage(), "Required tag MyFloatMissing not found within element"));
        }
    }

    public function testParseFloatMissingNotRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyFloat>1234.56</MyFloat>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->assertNull($this->_typeSafeParser->parseFloat($rootElement, "MyFloatMissing", false));
    }

    public function testParseString()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyString>Hello, World!</MyString>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $mystr = $this->_typeSafeParser->parseString($rootElement, "MyString");
        $this->assertEquals("Hello, World!", $mystr);
    }

    public function testParseStringMissingRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyString>Hello, World!</MyString>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        try {
            $this->_typeSafeParser->parseInt($rootElement, "MyStringMissing");
            $this->fail("No exception thrown for missing string tag");
        } catch (ParserException $e) {
            $this->assertEquals(0, strpos($e->getMessage(), "Required tag MyStringMissing not found within element"));
        }
    }

    public function testParseStringMissingNotRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyString>Hello, World!</MyString>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->assertNull($this->_typeSafeParser->parseString($rootElement, "MyStringMissing", false));
    }

    public function testParseBool()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <StrFalse>False</StrFalse>
    <StrTrue>true</StrTrue>
    <IntFalse>0</IntFalse>
    <IntTrue>1</IntTrue>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->assertFalse($this->_typeSafeParser->parseBool($rootElement, "StrFalse"), "StrFalse");
        $this->assertTrue($this->_typeSafeParser->parseBool($rootElement, "StrTrue"), "StrTrue");
        $this->assertFalse($this->_typeSafeParser->parseBool($rootElement, "IntFalse"), "IntFalse");
        $this->assertTrue($this->_typeSafeParser->parseBool($rootElement, "IntTrue"), "IntTrue");
    }

    public function testParseBoolAttribute()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyTag StrFalse="false" StrTrue="true" IntFalse="0" IntTrue="1"/>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myTag = $rootElement->MyTag;
        $this->assertFalse($this->_typeSafeParser->extractAttribute($myTag, "StrFalse", "boolean"), "StrFalse");
        $this->assertTrue($this->_typeSafeParser->extractAttribute($myTag, "StrTrue", "boolean"), "StrTrue");
        $this->assertFalse($this->_typeSafeParser->extractAttribute($myTag, "IntFalse", "boolean"), "IntFalse");
        $this->assertTrue($this->_typeSafeParser->extractAttribute($myTag, "IntTrue", "boolean"), "IntTrue");
    }

    public function testParseBoolAttributeInvalidValue()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyTag StrCrap="crap"/>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myTag = $rootElement->MyTag;
        try {
            $this->_typeSafeParser->extractAttribute($myTag, "StrCrap", "boolean");
            $this->fail("No exception thrown for invalid bool attribute value");
        } catch (ParserException $e) {
            $this->assertEquals("Unknown bool attribute value [crap] for attribute StrCrap", $e->getMessage());
        }
    }

    public function testParseBoolInvalidValue()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <StrInvalid>crap</StrInvalid>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        try {
            $this->_typeSafeParser->parseBool($rootElement, "StrInvalid");
            $this->fail("No exception thrown for invalid boolean value");
        } catch (ParserException $e) {
            $this->assertEquals("Unknown bool value: <StrInvalid>crap</StrInvalid>", $e->getMessage());
        }
    }

    public function testParseBoolMissingRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <StrFalse>False</StrFalse>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        try {
            $this->_typeSafeParser->parseBool($rootElement, "StrMissing");
            $this->fail("No exception thrown for invalid boolean value");
        } catch (ParserException $e) {
            $this->assertEquals(0, strpos($e->getMessage(), "Required tag StrMissing not found within element"));
        }
    }

    public function testParseBoolMissingNotRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <StrFalse>False</StrFalse>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->assertNull($this->_typeSafeParser->parseBool($rootElement, "StrMissing", false));
    }

    public function testExtractAttribute()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyTag myattribute="myvalue">False</MyTag>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myTag = $this->_typeSafeParser->extractElement($rootElement, "MyTag");
        $this->assertEquals($this->_typeSafeParser->extractAttribute($myTag, "myattribute"), "myvalue");
    }

    public function testExtractAttribute_default_noattr()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyTag>False</MyTag>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myTag = $this->_typeSafeParser->extractElement($rootElement, "MyTag");
        $this->assertEquals($this->_typeSafeParser->extractAttribute($myTag, "myattribute", "string", "defaultstr"), "defaultstr");
    }

    public function testExtractAttribute_default_attrset()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyTag myattribute="nondefaultstr">False</MyTag>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myTag = $this->_typeSafeParser->extractElement($rootElement, "MyTag");
        $this->assertEquals($this->_typeSafeParser->extractAttribute($myTag, "myattribute", "string", "defaultstr"), "nondefaultstr");
    }


    public function testExtractStringTag_RenderData()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<RenderData xmlns="http://www.moo.com/xsd/render-1.0">
    <Template xmlns="http://www.moo.com/xsd/template-1.0">
    </Template>
    <UserData>
        <!--suppress CheckTagEmptyBody -->
        <Settings></Settings>
        <Data>
            <TextData>
                <LinkId>textfield_1</LinkId>
                <Text>I am text from TextData</Text>
            </TextData>
        </Data>
    </UserData>   
</RenderData>    
    
XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->_typeSafeParser = new TypeSafeParser("http://www.moo.com/xsd/render-1.0");

        /**
         * @var \SimpleXMLElement $dataTag
         */
        $dataTag = $rootElement->UserData->Data;
        $this->assertEquals("SimpleXMLElement", get_class($dataTag));

        $dataTag->registerXPathNamespace("default", "http://www.moo.com/xsd/render-1.0");
        $textDataTag = $this->_typeSafeParser->extractElement($dataTag, "TextData");
        $this->assertEquals("SimpleXMLElement", get_class($textDataTag));

        $linkId = $this->_typeSafeParser->parseString($textDataTag, "LinkId");
        $this->assertEquals("textfield_1", $linkId);
    }

    public function testExtractStringTag_RenderData1_5()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<RenderData xmlns="http://www.moo.com/xsd/render-1.0">
    <Template xmlns="http://www.moo.com/xsd/template-1.0">
    </Template>
</RenderData>    
    
XML;
        $rootElement = new \SimpleXMLElement($testXml);

        $templateTag = $this->_typeSafeParser->extractElement($rootElement, "Template");
        $this->assertEquals("SimpleXMLElement", get_class($templateTag));

    }

    public function testExtractAttributeUnknownType()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyTag StrFalse="False" StrTrue="true" IntFalse="0" IntTrue="1"/>
</Root>

XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $myTag = $rootElement->MyTag;
        $this->assertEquals($this->_typeSafeParser->extractAttribute($myTag, "StrFalse", "crap"), "False", "StrFalse");
    }

    public function testExtractStringTag_RenderData2_1()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<RenderData xmlns="http://www.moo.com/xsd/render-1.0">
    <Template xmlns="http://www.moo.com/xsd/template-1.0">
    </Template>
    <UserData>
    </UserData>   
</RenderData>    
    
XML;
        $rootElement = new \SimpleXMLElement($testXml);

        $this->_typeSafeParser = new TypeSafeParser("http://www.moo.com/xsd/render-1.0");

        $userDataTag = $this->_typeSafeParser->extractElement($rootElement, "UserData");
        $this->assertEquals("SimpleXMLElement", get_class($userDataTag));
    }

    public function testExtractStringTag_RenderData2()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<RenderData xmlns="http://www.moo.com/xsd/render-1.0">
    <Template xmlns="http://www.moo.com/xsd/template-1.0">
    </Template>
    <UserData>
        <!--suppress CheckTagEmptyBody -->
        <Settings></Settings>
        <Data>
            <TextData>
                <LinkId>textfield_1</LinkId>
                <Text>I am text from TextData</Text>
            </TextData>
        </Data>
    </UserData>   
</RenderData>    
    
XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->_typeSafeParser = new TypeSafeParser("http://www.moo.com/xsd/render-1.0");

        $userDataTag = $this->_typeSafeParser->extractElement($rootElement, "UserData");
        $this->assertEquals("SimpleXMLElement", get_class($userDataTag));

        $dataTag = $this->_typeSafeParser->extractElement($userDataTag, "Data");
        $this->assertEquals("SimpleXMLElement", get_class($dataTag));

        //        $dataTag->registerXPathNamespace("default", "http://www.moo.com/xsd/render-1.0");
        $textDataTag = $this->_typeSafeParser->extractElement($dataTag, "TextData");
        $this->assertEquals("SimpleXMLElement", get_class($textDataTag));

        $linkId = $this->_typeSafeParser->parseString($textDataTag, "LinkId");
        $this->assertEquals("textfield_1", $linkId);
    }

    public function testMultipleElementsRequired()
    {
        $testXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Root xmlns="http://www.moo.com/xsd/template-1.0">
    <MyTag/>
    <MyTag/>
</Root>
    
XML;
        $rootElement = new \SimpleXMLElement($testXml);
        $this->_typeSafeParser = new TypeSafeParser("http://www.moo.com/xsd/template-1.0");
        try {
            $this->_typeSafeParser->extractElement($rootElement, "MyTag");
            $this->fail("No exception for multiple elements marked as required");
        } catch (ParserException $e) {
            $this->assertEquals(0, strpos($e->getMessage(), "More than one tag MyTag found within element"));
        }
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->_typeSafeParser = null;
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
    }

}

