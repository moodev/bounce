<?php

namespace MooDev\Bounce\Proxy;

use MooDev\Bounce\Context\ApplicationContext;

/**
 * Acts as a proxy between a context, and an item in the Zend_Registry.
 * This item is added to the registry under the given property name, and the
 * first time that anything retrieves the object from the Zend_Registry and
 * either uses a property or calls a method on it, the underlying object is
 * created *at that point* - i.e. lazily.
 *
 * This increases performance by not having to create objects needlessly for
 * every request, but still allows for easy configuration of everything.
 *
 * TODO In the fullness of time, this will probably be replaced by a more generic
 * proxying system, but this is the only current use of a proxy.
 *
 * @author steve
 */
class ZendRegistryReplacingProxy
{

    /**
     * @var string the name of the object in the Zend_Registry which we're proxying for
     */
    private $_registryProperty;

    /**
     * @var string the name of the target bean that we're proxying for.
     */
    private $_targetName;

    /**
     * @var ApplicationContext a context instance used to retrieve the real value
     */
    private $_context;

    public function __construct($registryProperty, $targetName, $context)
    {
        $this->_registryProperty = $registryProperty;
        $this->_targetName = $targetName;
        $this->_context = $context;
    }

    public function __get($name)
    {
        //Replace the instance
        $targetObj = $this->_replaceInstance();
        return $targetObj->$name;
    }

    public function __set($name, $value)
    {
        //Replace the instance
        $targetObj = $this->_replaceInstance();
        $targetObj->$name = $value;
    }

    public function __call($name, $arguments)
    {
        //Replace the instance
        $targetObj = $this->_replaceInstance();
        return call_user_func_array(array($targetObj, $name), $arguments);
    }

    private function _replaceInstance()
    {
        $targetObj = $this->_context->get($this->_targetName);
        \Zend_Registry::getInstance()->set($this->_registryProperty, $targetObj);
        return $targetObj;
    }
}
