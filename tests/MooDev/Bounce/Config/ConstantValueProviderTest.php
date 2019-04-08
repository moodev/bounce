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
 * ConstantValueProvider test case.
 */
class ConstantValueProviderTest extends TestCase
{

    public function testConstantRetrieval()
    {
        define("STEVE_TEST", "HELLO!");
        $impl = new ConstantValueProvider("STEVE_TEST");
        $this->assertEquals("HELLO!", $impl->getValue(BeanFactory::getInstance(new Context())));
    }
}

