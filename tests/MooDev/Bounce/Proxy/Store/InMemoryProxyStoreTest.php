<?php
namespace MooDev\Bounce\Proxy\Store;
use Mockery as m;

require_once __DIR__ . '/../../../../TestInit.php';

class InMemoryProxyStoreTest extends \PHPUnit_Framework_TestCase {

    public function testDefaultNamespaceIsValid()
    {
        $store = new UniqTmpDirFilesystemProxyStore();
        $ns = $store->getProxyNamespace();
        eval("namespace $ns;");
    }

}

