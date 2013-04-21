<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Proxy;

use MooDev\Bounce\Config\Bean;
use MooDev\Bounce\Exception\BounceException;
use MooDev\Bounce\Proxy\CG\CallBuilder;
use MooDev\Bounce\Proxy\CG\ClassBuilder;
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
     * @var string
     */
    private $_proxyNS;

    /**
     * @var string
     */
    private $_proxyDir;

    /**
     * @var string
     */
    private $_uniqueId;

    /**
     * @param string $proxyDir Directory under which the proxies will be created.
     * @param string $proxyNS Namespace under which the proxies will be created.
     * @param string $uniqueId string which will be added to the directory and namespace.
     */
    function __construct($proxyDir, $proxyNS, $uniqueId)
    {
        $this->_proxyDir = $proxyDir;
        $this->_proxyNS = $proxyNS;
        if (!empty($uniqueId)) {
            $this->_uniqueId = $this->_makeSafeStr($uniqueId);
        }
    }

    protected function _makeSafeStr($str) {
        $str = base64_encode($str);
        $str = strtr($str, '=+', "\x80\x81");
        return $str;
    }

    protected function _nameForProxy($beanName) {
        return $this->_makeSafeStr($beanName);
    }

    protected function _namespaceForProxy()
    {
        return $this->_proxyNS . (isset($this->_uniqueId) ? ('\\' . $this->_uniqueId) : '');
    }

    protected function _nameToFilename($proxyName) {
        return $this->_proxyDir . DIRECTORY_SEPARATOR . (isset($this->_uniqueId) ? ($this->_uniqueId  . DIRECTORY_SEPARATOR) : '') . $proxyName . '.php';
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
            // OK, we need to include it
            $rClass = new \ReflectionClass($definition->class);

            $file = $this->_nameToFilename($this->_nameForProxy($definition->name));
            if (($rClass->getFileName() !== false && filemtime($rClass->getFileName()) >= @filemtime($file)) ||
                    !@include($file) ||
                    !class_exists($fullName, false)) {
                // File out of date (or was internal), or could not include the file, or it didn't define our proxy. Regenerate it.
                $tmpFile = $this->generateProxy($definition, $rClass);
                @rename($tmpFile, $file);
                // This time we should require it, as it really ought to be there.
                require($file);
            }
        }
        return $fullName;
    }

    /**
     * Generate a proxy class and write it to a file.
     * @param Bean $definition Bean to create the proxy for.
     * @param \ReflectionClass $rClass The class we're proxying (if null, we'll use the Bean's class.)
     * @return string Full path to a file containing the generated class.
     * @throws \MooDev\Bounce\Exception\BounceException
     */
    public function generateProxy(Bean $definition, \ReflectionClass $rClass = null) {
        $proxyName = $this->_nameForProxy($definition->name);
        if (!isset($rClass)) {
            $rClass = new \ReflectionClass($definition->class);
        }

        $constructorBuilder = MethodBuilder::build("__construct")
            ->addParam(new Param("__bounceBeanFactory", false, null, '\MooDev\Bounce\Context\BeanFactory'))
            ->addLine('$this->__bounceBeanFactory = $__bounceBeanFactory;');

        $rCon = $rClass->getConstructor();
        if (isset($rCon)) {
            $rParams = $rCon->getParameters();
            $superCallBuilder = CallBuilder::build("parent::__construct");
            foreach ($rParams as $param) {
                if ($param->isPassedByReference()) {
                    throw new BounceException("Cannot proxy methods which require pass by reference");
                }
                $superCallBuilder->addParam('$' . $param->getName());
                $constructorBuilder->addParam(new Param($param->getName(), $param->isOptional(), ($param->isOptional() ? $param->getDefaultValue() : false)));
            }
            $constructorBuilder->addLine($superCallBuilder->getCall() . ';');
        }

        $classBuilder = ClassBuilder::build($proxyName, $this->_namespaceForProxy())
            ->extend('\\' . $rClass->getName())
            ->addProperty(new Property("__bounceBeanFactory", "null", "private"))
            ->addMethod($constructorBuilder->getMethod());

        foreach ($definition->lookupMethods as $lookup) {
            $classBuilder->addMethod(MethodBuilder::build($lookup->name)
                ->addLine('return $this->__bounceBeanFactory->createByName(\'' . $lookup->bean . '\');'."\n")
                ->getMethod());
        }

        $file = $this->_nameToFilename($proxyName);
        $baseDir = dirname($file);
        @mkdir($baseDir, 0777, true);

        $tmpFile = tempnam($baseDir, basename($file));
        if ($tmpFile === false) {
            throw new BounceException("Unable to write proxy temp file");
        }
        $code = '<?php'."\n\n".$classBuilder->getClass();
        $wrote = file_put_contents($tmpFile, $code);
        if ($wrote != strlen($code)) {
            unlink($tmpFile);
            throw new BounceException("Unable to write proxy temp file. Wrote $wrote bytes but expected " . strlen($code));
        }
        return $tmpFile;
    }

}