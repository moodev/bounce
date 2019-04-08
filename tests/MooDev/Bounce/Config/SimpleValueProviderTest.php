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
 * SimpleValueProvider test case.
 */
class SimpleValueProviderTest extends TestCase
{

    public function testSimpleValue()
    {
        $factory = BeanFactory::getInstance(new Context());
        $impl = new SimpleValueProvider("steve");
        $this->assertEquals("steve", $impl->getValue($factory));
        $impl = new SimpleValueProvider(false);
        $this->assertFalse($impl->getValue($factory));
        $impl = new SimpleValueProvider(2);
        $this->assertEquals(2, $impl->getValue($factory));
        $impl = new SimpleValueProvider(56.789);
        $this->assertEquals(56.789, $impl->getValue($factory));
    }
}

