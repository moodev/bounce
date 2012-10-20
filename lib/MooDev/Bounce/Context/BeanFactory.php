<?php
/**
 * @author Steve Storey <steves@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Context;

use \MooDev\Bounce\Config;
use ReflectionObject;
use ReflectionClass;
use \MooDev\Bounce\Exception\BounceException;

/**
 * The job of the bean factory is to do the heavy lifting of creating the
 * beans defined by the config objects.
 *
 * @property Config\Context contextConfig
 */
class BeanFactory
{

    /**
     * @var string -> BeanFactory instances 
     */
    private static $_globalFactories = array();
    
    /**
     * Returns an instance of the BeanFactory, using the globally shared version
     * if applicable.
     *
     * @param Config\Context $context
     * @param bool $globalShared [true] whether to use a global shared factory if possible
     * @return \MooDev\Bounce\Context\BeanFactory
     */
    public static function getInstance(Config\Context $context, $globalShared = true)
    {
        //Do we have a unique ID? If not, then we're not shared.
        if ($globalShared && !is_null($context->uniqueId)) {
            //If we're not in the shared list yet, make it so
            if (!array_key_exists($context->uniqueId, self::$_globalFactories)) {
                $factory = new BeanFactory();
                $factory->_contextConfig = $context;
                //Remember this in the shared cache
                self::$_globalFactories[$context->uniqueId] = $factory;
            }
            return self::$_globalFactories[$context->uniqueId];
        } else {
            $factory = new BeanFactory();
            $factory->_contextConfig = $context;
            return $factory;
        }
    }

    /**
     * @var Config\Context the Context data to be used to create
     * instances.
     */
    protected $_contextConfig;
    /**
     * @var string => object mapping of configured instances
     */
    protected $_configuredInstances = array();
    
    /**
     * @var string => Config\Context mapping of bean names to the
     * containing child context, or null if not applicable.
     */
    private $_childContextsByName = array();
    

    private function __construct()
    {
        //Does nothing
    }

    public function __set($name, $value)
    {
        if ($name == "contextConfig") {
            if ($value instanceof Config\Context) {
                $this->_contextConfig = $value;
            } else {
                throw new BounceException('Invalid contextConfig. Must be a Config\Context instance');
            }
        }
    }

    /**
     * Returns a fully populated object based on the context configuration
     *
     * @param $name string the name of the bean to be retrieved
     * @param BeanFactory $referenceFactory referenceFactory factory which should be
     * used to resolve references contained within this bean definition. This will default to the
     * current factory if left null or not supplied. This will be used in the case where a parent
     * context is resolving a bean from a child context but therefore needs to supply itself as
     * the parent so that the import'ed context can refer to beans which are defined on the parent.
     * @throws BounceException
     * @return object the fully-populated object ready to be used by the application
     */
    public function createByName($name, BeanFactory $referenceFactory = null)
    {
        if (is_null($this->_contextConfig)) {
            throw new BounceException("No configuration provided to bean factory");
        }
        if (is_null($referenceFactory)) {
            $referenceFactory = $this;
        }
        //Have we already created and configured this? If not, look to see
        //if the configuration defines it, and create if so. If neither, then it's
        //exception time.
        if (array_key_exists($name, $this->_configuredInstances)) {
            //Use the pre-configured item
            return $this->_configuredInstances[$name];
        } elseif (array_key_exists($name, $this->_contextConfig->beans)) {
            return $this->create($this->_contextConfig->beans[$name], $referenceFactory);
        } else {
            //It wasn't an immediate child of us, so try to find the factory which
            //has the bean
            $childFactory = $this->_getChildFactoryFor($name);
            if (!is_null($childFactory)) {
                return $childFactory->createByName($name, $referenceFactory);
            } else {
                throw new BounceException("No object defined with name $name");
            }
        }
    }

    public function create(Config\Bean $definition, BeanFactory $referenceFactory = null)
    {
        if (is_null($referenceFactory)) {
            $referenceFactory = $this;
        }
        //Just instantiate the object
        $object = $this->_instantiate($definition, $referenceFactory);
        //If we have a name on this bean, it's global, so
        //save it so as to deal with circular references
        if (!is_null($definition->name) && trim($definition->name) != "") {
            if ($definition->scope != "prototype") {
                // Only save it if we're not supposed to re-instantiate it each time
                $this->_configuredInstances[$definition->name] = $object;
            }
        }
        //Now load up all the properties onto the object
        $this->_configureProperties($definition, $object, $referenceFactory);

        if ($object instanceof Config\Configurable) {
            $object->configure();
        }

        return $object;
    }


    /**
     * @param string $class The name of the class to instantiate
     * @param array $args The arguments to pass to the constructor
     * @return object An instance of $class
     */
    protected function _instantiateByConstructor($class, $args) {
        switch (count($args)) {
            case 0:
                return new $class();
            case 1:
                return new $class($args[0]);
            case 2:
                return new $class($args[0], $args[1]);
            case 3:
                return new $class($args[0], $args[1], $args[2]);
            case 4:
                return new $class($args[0], $args[1], $args[2], $args[3]);
        }
        $class = new ReflectionClass($class);
        return $class->newInstanceArgs($args);
    }

    /**
     * @param object $factoryBean The instance to call the factory method on
     * @param string $factoryMethod The name of the method to invoke
     * @param array $args The arguments to pass to the method
     * @return object The object returned by the factory
     */
    protected function _instantiateByFactoryBean($factoryBean, $factoryMethod, $args) {
        switch (count($args)) {
            case 0:
                return $factoryBean->$factoryMethod();
            case 1:
                return $factoryBean->$factoryMethod($args[0]);
            case 2:
                return $factoryBean->$factoryMethod($args[0], $args[1]);
            case 3:
                return $factoryBean->$factoryMethod($args[0], $args[1], $args[2]);
            case 4:
                return $factoryBean->$factoryMethod($args[0], $args[1], $args[2], $args[3]);
        }
        $object = new ReflectionObject($factoryBean);
        /**
         * @var \ReflectionMethod $method
         */
        $method = $object->getMethod($factoryMethod);
        return $method->invokeArgs($factoryBean, $args);
    }

    /**
     * @param string $class The name of the class to call the static on
     * @param string $factoryMethod The name of the static method
     * @param array $args The arguments to pass to the method
     * @return object The object returned by the factory
     */
    protected function _instantiateByStaticFactory($class, $factoryMethod, $args) {
        switch (count($args)) {
            case 0:
                return $class::$factoryMethod();
            case 1:
                return $class::$factoryMethod($args[0]);
            case 2:
                return $class::$factoryMethod($args[0], $args[1]);
            case 3:
                return $class::$factoryMethod($args[0], $args[1], $args[2]);
            case 4:
                return $class::$factoryMethod($args[0], $args[1], $args[2], $args[3]);
        }
        $class = new ReflectionClass($class);
        /**
         * @var \ReflectionMethod $method
         */
        $method = $class->getMethod($factoryMethod);
        return $method->invokeArgs(null, $args);
    }

    /**
     * Creates the instance based on the Bean definition, but without
     * any properties being configured yet. This is so that in the case
     * of circular references within Bean's, we can take care of that possibility
     *
     * @param $definition Config\Bean the definition of the bean
     * @param BeanFactory $referenceFactory factory which should be
     * used to resolve references contained within this bean definition. This will be used in the
     * case where a parent context is resolving a bean from a child context but therefore needs to
     * supply itself as the parent so that the import'ed context can refer to beans which are
     * defined on the parent.
     * @return object the object created with the relevant constructor arguments
     */
    protected function _instantiate(Config\Bean $definition, BeanFactory $referenceFactory)
    {

        //Look at whether there are constructor args defined. If so, we
        //need to get the values for them
        $constructorArgs = array();
        foreach ($definition->constructorArguments as $valueProvider) {
            $constructorArgs[] = $valueProvider->getValue($referenceFactory);
        }
        $class = $definition->class;
        $args = $constructorArgs;

        if (isset($definition->factoryMethod)) {
            $factoryMethod = $definition->factoryMethod;
            if (isset($definition->factoryBean)) {
                // We need to look up the factory bean, then use it to create our instance.
                $factoryBean = $this->createByName($definition->factoryBean, $referenceFactory);
                return $this->_instantiateByFactoryBean($factoryBean, $factoryMethod, $args);
            } else {
                // We need to invoke a static factory on the named class
                return $this->_instantiateByStaticFactory($class, $factoryMethod, $args);
            }
        } else {

            if (!empty($definition->lookupMethods)) {
                // We need to proxy!
                $class = $this->_createProxy($definition);
            }
            // Make our own instance
            return $this->_instantiateByConstructor($class, $args);
        }
    }

    protected function _createProxy(Config\Bean $definition) {
        $class = $definition->class;

        $proxyNS = 'MooDev\Bounce\Temp\Proxy';
        $proxyClass = "Bounce_" . spl_object_hash($this) . "_Proxy_$class";
        $fullName = '\\' . $proxyNS . '\\' . $proxyClass;

        if (class_exists($fullName, false)) {
            return $fullName;
        }

        // TODO: configurable dir and cleanup
        $tmpFile = tempnam(sys_get_temp_dir(), $proxyClass);


        $proxyClassCode = "<?php\n\nnamespace ".$proxyNS.";\n\nclass " . $proxyClass . " {\n    public static \$bounceBeanFactory;\n\n";

        foreach ($definition->lookupMethods as $lookup) {

            $proxyClassCode .= "    public function " . $lookup->name . "() {\n";
            $proxyClassCode .= "        return self::\$bounceBeanFactory->createByName('" . $lookup->bean . "');\n";
            $proxyClassCode .= "    }\n\n";
        }

        $proxyClassCode .= "\n}\n\n";

        file_put_contents($tmpFile, $proxyClassCode);
        /** @noinspection PhpIncludeInspection */
        require($tmpFile);

        /** @noinspection PhpUndefinedVariableInspection */
        $fullName::$bounceBeanFactory = $this;

        return $fullName;
    }

    /**
     * Configures the properties defined on the given object from the bean definition
     * resolving any references with the provided factory.
     * @param Config\Bean $definition the definition of the bean being created.
     * @param mixed $object the instantiated object requiring its properties to be set.
     * @param BeanFactory $referenceFactory factory which should be
     * used to resolve references contained within this bean definition. This will be used in the
     * case where a parent context is resolving a bean from a child context but therefore needs to
     * supply itself as the parent so that the import'ed context can refer to beans which are
     * defined on the parent.
     */
    protected function _configureProperties(Config\Bean $definition, $object, BeanFactory $referenceFactory)
    {
        foreach ($definition->properties as $propertyName => $valueProvider) {
            $object->$propertyName = $valueProvider->getValue($referenceFactory);
        }
    }
    
    /**
     * Returns the factory to be used for the bean with the given name from all the descendent contexts.
     *
     * @param string $name the name of the bean we wish to create.
     * @return BeanFactory the factory which will be able to create this
     * bean.
     */
    protected function _getChildFactoryFor($name)
    {
        $childContext = $this->_getChildContextFor($name, $this->_contextConfig);
        if (!is_null($childContext)) {
            return BeanFactory::getInstance($childContext);
        } else {
            return null;
        }
    }
    
    /**
     * Returns the context which directly contains the bean with the given name. Note that we
     * explicitly don't search ourselves.
     *
     * @param string $name the name of the bean we wish to look for.
     * @param Config\Context $parentContext the parent context
     * whose children we should be searching.
     * @return Config\Context the child context of the parent
     * which defines the bean
     */
    protected function _getChildContextFor($name, Config\Context $parentContext)
    {
        if (!array_key_exists($name, $this->_childContextsByName)) {
            //We need to find the answer. We default to null
            $this->_childContextsByName[$name] = null;
            foreach ($parentContext->childContexts as $context) {
                if (array_key_exists($name, $context->beans)) {
                    $this->_childContextsByName[$name] = $context;
                }
                //Otherwise see if it has this bean in one of its child contexts
                $grandChildContext = $this->_getChildContextFor($name, $context);
                if (!is_null($grandChildContext)) {
                    $this->_childContextsByName[$name] = $grandChildContext;
                }
            }
        }
        return $this->_childContextsByName[$name];
    }
}
