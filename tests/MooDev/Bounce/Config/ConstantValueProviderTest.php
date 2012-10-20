<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

require_once __DIR__ . '/../../../TestInit.php';

/**
 * ConstantValueProvider test case.
 */
class ConstantValueProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testConstantRetrieval()
    {
        define("STEVE_TEST", "HELLO!");
        $impl = new ConstantValueProvider("STEVE_TEST");
        $this->assertEquals("HELLO!", $impl->getValue(BeanFactory::getInstance(new Context())));
    }
}

