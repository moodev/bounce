<?php
namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

require_once __DIR__ . '/../../../TestInit.php';

/**
 * MapValueProvider test case.
 */
class MapValueProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyMap()
    {
        $impl = new MapValueProvider(array());
        $this->assertEquals(array(), $impl->getValue(BeanFactory::getInstance(new Context())));
    }

    public function testSimpleList()
    {
        $impl = new MapValueProvider(array(
            "one" => new SimpleValueProvider("steve"),
            "two" => new SimpleValueProvider("jon"),
            "three" => new SimpleValueProvider("roly"),
        ));
        $this->assertEquals(array("one" => "steve", "two" => "jon", "three" => "roly"), $impl->getValue(BeanFactory::getInstance(new Context())));
    }
}

