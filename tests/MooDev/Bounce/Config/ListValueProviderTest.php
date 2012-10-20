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
 * ListValueProvider test case.
 */
class ListValueProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyList()
    {
        $impl = new ListValueProvider(array());
        $this->assertEquals(array(), $impl->getValue(BeanFactory::getInstance(new Context())));
    }

    public function testSimpleList()
    {
        $impl = new ListValueProvider(array(
            new SimpleValueProvider("steve"),
            new SimpleValueProvider("jon"),
            new SimpleValueProvider("roly"),
        ));
        $this->assertEquals(array("steve", "jon", "roly"), $impl->getValue(BeanFactory::getInstance(new Context())));
    }
}

