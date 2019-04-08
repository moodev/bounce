<?php
namespace MooDev\Bounce\Proxy\Store;
use Mockery as m;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../TestInit.php';

class InMemoryProxyStoreTest extends TestCase {

    public function testDefaultNamespaceIsValid()
    {
        $store = new UniqTmpDirFilesystemProxyStore();
        $ns = $store->getProxyNamespace();
        eval("namespace $ns;");
    }

}

