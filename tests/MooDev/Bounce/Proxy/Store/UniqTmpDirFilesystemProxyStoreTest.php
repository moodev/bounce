<?php
namespace MooDev\Bounce\Proxy\Store;
use Mockery as m;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../TestInit.php';

class UniqTmpDirFilesystemProxyStoreTest extends TestCase {

    public function testStoreAndImport()
    {
        $store = new UniqTmpDirFilesystemProxyStore();
        $file = $store->store("a", "testStoreAndImport", 'global $a; $a = 2;', 75);
        $this->assertFileExists($file);
        $this->assertTrue($store->import("a", "testStoreAndImport", 75));
        global $a;
        $this->assertEquals(2, $a);
        $store = null;
        $this->assertFileNotExists(dirname($file)); // Check cleanup
    }

    public function testDefaultNamespaceIsValid()
    {
        $store = new UniqTmpDirFilesystemProxyStore();
        $ns = $store->getProxyNamespace();
        $this->assertSame(113, strlen($ns));
    }

}

