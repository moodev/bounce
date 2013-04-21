<?php
namespace MooDev\Bounce\Proxy;


use MooDev\Bounce\Config\Bean;
use MooDev\Bounce\Exception\BounceException;
use MooDev\Bounce\Proxy\CG\CallBuilder;
use MooDev\Bounce\Proxy\CG\ClassBuilder;
use MooDev\Bounce\Proxy\CG\MethodBuilder;
use MooDev\Bounce\Proxy\CG\Param;
use MooDev\Bounce\Proxy\CG\Property;

class LookupMethodProxyGenerator {

    private $_proxyNS;
    private $_proxyDir;
    private $_uniqueId;

    function __construct($_proxyDir, $_proxyNS, $_uniqueId)
    {
        $this->_proxyDir = $_proxyDir;
        $this->_proxyNS = $_proxyNS;
        if (!empty($_uniqueId)) {
            $this->_uniqueId = $this->_makeSafeStr($_uniqueId);
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

    public function loadProxy(Bean $definition) {
        $fullName = $this->_fullyQualifiedClassName($definition->name);
        if (!class_exists($fullName, false)) {
            $rClass = new \ReflectionClass($definition->class);

            $file = $this->_nameToFilename($this->_nameForProxy($definition->name));
            if (($rClass->getFileName() !== false && filemtime($rClass->getFileName()) >= @filemtime($file)) ||
                    !@include($file) ||
                    !class_exists($fullName, false)) {
                // File out of date, or could not include the file, or it didn't define our proxy. Regenerate it.
                $tmpFile = $this->generateProxy($rClass, $definition);
                @rename($tmpFile, $file);
                require($file);
            }
        }
        return $fullName;
    }

    public function generateProxy(\ReflectionClass $rClass, Bean $definition) {
        $proxyName = $this->_nameForProxy($definition->name);

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
            ->addProperty(new Property("__bounceBeanFactory", null, "private"))
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