<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;

require_once __DIR__ . '/../../../TestInit.php';

/**
 * NullValueProvider test case.
 */
class NullValueProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testNullValue()
    {
        $impl = new NullValueProvider();
        $this->assertNull($impl->getValue(BeanFactory::getInstance(new Context())));
    }
}

