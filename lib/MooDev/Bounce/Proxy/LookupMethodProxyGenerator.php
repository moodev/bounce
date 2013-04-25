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

    private static $_BASE32MAP = array(
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A",
        "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L",
        "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V",
    );

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

    /**
     * Take a string and make a new string that's safe for use to use in file and class names.
     * @param string $str
     * @return string
     */
    protected function _makeSafeStr($str) {
        // Yeah, so, err, let's use a Base32 variant to encode our string.
        $modulus = 0;
        $bitWorkArea = 0;
        $out = "b"; // need to make sure the first char of the string is valid for a class name.
        $mask = 0x1f;
        foreach (str_split($str) as $chr) {
            $modulus = ($modulus + 1) % 5;
            $b = ord($chr);
            $bitWorkArea = ($bitWorkArea << 8) + $b;
            if ($modulus == 0) {
                $out .= base_convert(($bitWorkArea >> 35) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 30) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 25) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 20) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 15) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 10) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 5) & $mask, 10, 32);
                $out .= base_convert($bitWorkArea & $mask, 10, 32);
            }
        }
        switch ($modulus) {
            case 1 :
                $out .= base_convert(($bitWorkArea >> 3) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea << 2) & $mask, 10, 32);
                break;
            case 2 :
                $out .= base_convert(($bitWorkArea >> 11) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >>  6) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >>  1) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea <<  4) & $mask, 10, 32);
                break;
            case 3 :
                $out .= base_convert(($bitWorkArea >> 19) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 14) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >>  9) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >>  4) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea <<  1) & $mask, 10, 32);
                break;
            case 4 :
                $out .= base_convert(($bitWorkArea >> 27) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 22) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 17) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >> 12) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >>  7) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea >>  2) & $mask, 10, 32);
                $out .= base_convert(($bitWorkArea <<  3) & $mask, 10, 32);
                break;
        }
        return $out;
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
            $rClass = new \ReflectionClass($definition->class);

            $file = $this->_nameToFilename($this->_nameForProxy($definition->name));
            /** @noinspection PhpIncludeInspection */
            if (($rClass->getFileName() !== false && filemtime($rClass->getFileName()) >= @filemtime($file)) ||
                    !@include($file) ||
                    !class_exists($fullName, false)) {
                // File out of date (or was internal), or could not include the file, or it didn't define our proxy. Regenerate it.
                $tmpFile = $this->generateProxy($definition, $rClass);
                @rename($tmpFile, $file);
                // This time we should require it, as it really ought to be there.
                /** @noinspection PhpIncludeInspection */
                require($file);
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
            ->addParam(new Param("__bounceBeanFactory", false, null, '\MooDev\Bounce\Context\BeanFactory'))
            ->addLine('$this->__bounceBeanFactory = $__bounceBeanFactory;');

        $rCon = $rClass->getConstructor();
        if (isset($rCon)) {
            // We need to add all of the super's constructor params to our constructor, and call parent::__construct.

            $rParams = $rCon->getParameters();
            $superCallBuilder = CallBuilder::build("parent::__construct");
            foreach ($rParams as $param) {
                $superCallBuilder->addParam('$' . $param->getName());
                $constructorBuilder->addParam(
                    new Param(
                        $param->getName(),
                        $param->isOptional(),
                        ($param->isOptional() ? $param->getDefaultValue() : "null"),
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

    /**
     * Generate a proxy class and write it to a file.
     * @param Bean $definition Bean to create the proxy for.
     * @param \ReflectionClass $rClass The class we're proxying (if null, we'll use the Bean's class.)
     * @return string Full path to a file containing the generated class.
     * @throws \MooDev\Bounce\Exception\BounceException
     */
    public function generateProxy(Bean $definition, \ReflectionClass $rClass = null) {

        // Get a Class for our proxy.
        $class = $this->generateProxyClass($definition, $rClass);

        // And write it to the right file.
        $file = $this->_nameToFilename($this->_nameForProxy($definition->name));
        $baseDir = dirname($file);
        @mkdir($baseDir, 0777, true);

        $tmpFile = tempnam($baseDir, basename($file));
        if ($tmpFile === false) {
            throw new BounceException("Unable to write proxy temp file");
        }
        $code = '<?php'."\n\n".$class;
        $wrote = file_put_contents($tmpFile, $code);
        if ($wrote != strlen($code)) {
            unlink($tmpFile);
            throw new BounceException("Unable to write proxy temp file. Wrote $wrote bytes but expected " . strlen($code));
        }
        return $tmpFile;
    }

}