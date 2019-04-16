<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Config;
use MooDev\Bounce\Context\BeanFactory;
use MooDev\Bounce\Exception\BounceException;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../TestInit.php';

/**
 * FilePathValueProvider test case.
 */
class FilePathValueProviderTest extends TestCase
{

    public function testGoodFilepath()
    {
        //Have to keep the root dir within the moocommon-infra area in order for Hudson to
        //correctly work as well
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        $impl = new FilePathValueProvider("build.xml");
        $this->assertTrue(file_exists($impl->getValue(BeanFactory::getInstance(new Context()))));
    }

    public function testBadFilepath()
    {
        //Have to keep the root dir within the moocommon-infra area in order for Hudson to
        //correctly work as well
        if (!defined("DOC_DIR")) {
            define("DOC_DIR", realpath(__DIR__ . '/../../../'));
        }
        $impl = new FilePathValueProvider("../moo-common-log/build.xml");
        $this->expectException(BounceException::class);
        $impl->getValue(BeanFactory::getInstance(new Context()));
    }
}

