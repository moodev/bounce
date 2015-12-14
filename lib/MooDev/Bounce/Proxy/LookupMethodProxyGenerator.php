<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Proxy;

use MooDev\Bounce\Config\Bean;
use MooDev\Bounce\Proxy\CG\CallBuilder;
use MooDev\Bounce\Proxy\CG\ClassBuilder;
use MooDev\Bounce\Proxy\CG\DClass;
use MooDev\Bounce\Proxy\CG\MethodBuilder;
use MooDev\Bounce\Proxy\CG\Param;
use MooDev\Bounce\Proxy\CG\Property;

/**
 * Class LookupMethodProxyGenerator
 * A generator for proxies used for providing lookup method functionality.
 * @package MooDev\Bounce\Proxy
 */
class LookupMethodProxyGenerator {

    /**
     * @var ProxyStore
     */
    private $_proxyStore;

    /**
     * @var string
     */
    private $_uniqueId;

    /**
     * @param ProxyStore $proxyStore Storage for proxies.
     * @param string $uniqueId string which will be added to the directory and namespace.
     */
    function __construct(ProxyStore $proxyStore, $uniqueId)
    {
        $this->_proxyStore = $proxyStore;
        if (!empty($uniqueId)) {
            $this->_uniqueId = $this->_makeSafeStr($uniqueId);
        }
    }

    /**
     * Take a string and make a new string that's safe for use to use in file and class names.
     * @param string $str
     * @return string
     */
    protected function _makeSafeStr($str) {
        // Prefix with a letter to ensure that we meet the rules for PHP class names.
        return "B" . Utils\Base32Hex::encode($str);
    }

    protected function _nameForProxy($beanName) {
        return $this->_makeSafeStr($beanName);
    }

    protected function _namespaceForProxy()
    {
        return $this->_proxyStore->getProxyNamespace() . (isset($this->_uniqueId) ? ('\\' . $this->_uniqueId) : '');
    }

    protected function _fullyQualifiedClassName($beanName)
    {
        return '\\' . $this->_namespaceForProxy() . '\\' . $this->_nameForProxy($beanName);
    }

    /**
     * @param Bean $definition Configuration of the bean we need to proxy for.
     * @return string fully qualified name of the proxy.
     */
    public function loadProxy(Bean $definition) {
        $fullName = $this->_fullyQualifiedClassName($definition->name);

        if (!class_exists($fullName, false)) {
            $rClass = new \ReflectionClass($definition->class);
            $proxyName = $this->_nameForProxy($definition->name);
            $lastModified = filemtime($rClass->getFileName());
            if (!$this->_proxyStore->import($proxyName, $this->_uniqueId, $lastModified) || !class_exists($fullName, false)) {
                // Stored version out of date (or was internal), or could not include it, or it didn't define our proxy. Regenerate it.
                $code = $this->generateProxyClass($definition, $rClass);
                $this->_proxyStore->storeAndImport($this->_uniqueId, $proxyName, $code, $lastModified);
            }
        }
        return $fullName;
    }

    /**
     * Generate a proxy class.
     * @param Bean $definition Bean to create the proxy for.
     * @param \ReflectionClass $rClass The class we're proxying (if null, we'll use the Bean's class.)
     * @return DClass the generated class.
     * @throws \MooDev\Bounce\Exception\BounceException
     */
    public function generateProxyClass(Bean $definition, \ReflectionClass $rClass = null) {
        $proxyName = $this->_nameForProxy($definition->name);
        if (!isset($rClass)) {
            $rClass = new \ReflectionClass($definition->class);
        }

        // We want to generate a proxy which wraps the super's constructor, with an extra bonus param containing
        // our BeanFactory. We can store that on a property, and implement our lookup methods using it.

        $classBuilder = ClassBuilder::build($proxyName, $this->_namespaceForProxy())
            ->extend('\\' . $rClass->getName())
            ->addProperty(new Property("__bounceBeanFactory", "null", "private"));

        $constructorBuilder = MethodBuilder::build("__construct")
            ->addParam(new Param("__bounceBeanFactory", false, null, '\MooDev\Bounce\Context\IBeanFactory'))
            ->addLine('$this->__bounceBeanFactory = $__bounceBeanFactory;');

        $rCon = $rClass->getConstructor();
        if (isset($rCon)) {
            // We need to add all of the super's constructor params to our constructor, and call parent::__construct.

            $rParams = $rCon->getParameters();
            $superCallBuilder = CallBuilder::build("parent::__construct");
            foreach ($rParams as $param) {
                $superCallBuilder->addParam('$' . $param->getName());
                $default = "null";
                if ($param->isDefaultValueAvailable()) {
                    $default = var_export($param->getDefaultValue(), true);
                }
                $constructorBuilder->addParam(
                    new Param(
                        $param->getName(),
                        $param->isOptional(),
                        $default,
                        null, // Type hint information isn't available from ReflectionParameter for some reason :-(
                        $param->isPassedByReference()));
            }

            $constructorBuilder->addLine($superCallBuilder->getCall() . ';');
        }

        $classBuilder->addMethod($constructorBuilder->getMethod());

        // And finally, let's implement the lookup methods we've been asked for.
        // These simply call createByName() on the BeanFactory.
        foreach ($definition->lookupMethods as $lookup) {
            $classBuilder->addMethod(MethodBuilder::build($lookup->name)
                ->addLine('return $this->__bounceBeanFactory->createByName(\'' . $lookup->bean . '\');'."\n")
                ->getMethod());
        }

        return $classBuilder->getClass();
    }

}