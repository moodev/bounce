<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */

namespace MooDev\Bounce\Proxy\CG;

/**
 * Builder for Class objects.
 * Class ClassBuilder
 * @package MooDev\Bounce\Proxy\CG
 */
class ClassBuilder {

    /**
     * @var string
     */
    private $_class = null;

    /**
     * @var string
     */
    private $_namespace = null;

    /**
     * @var Property[]
     */
    private $_properties = array();

    /**
     * @var Method[]
     */
    private $_methods = array();

    /**
     * @var string[]
     */
    private $_implements = array();

    /**
     * @var string[]
     */
    private $_extends = array();

    /**
     * Get a builder.
     * @param string $name Name of the class we're building.
     * @param string $namespace Namespace that the class lives in.
     * @return ClassBuilder
     */
    public static function build($name, $namespace = null) {
        return new ClassBuilder($name, $namespace);
    }

    /**
     * Construct a builder.
     * @param string $name Name of the class we're building.
     * @param string $namespace Namespace that the class lives in.
     */
    public function __construct($name, $namespace = null) {
        $this->_namespace = $namespace;
        $this->_class = $name;
    }

    /**
     * @param string $class Fully qualified of a class to extend.
     * @return ClassBuilder
     */
    public function extend($class) {
        $this->_extends[] = $class;
        return $this;
    }

    /**
     * @param string $interface Fully qualified name of an interface to implement.
     * @return ClassBuilder
     */
    public function implement($interface) {
        $this->_implements[] = $interface;
        return $this;
    }

    /**
     * @param Method $method Method to add to our class.
     * @return ClassBuilder
     */
    public function addMethod(Method $method) {
        $this->_methods[] = $method;
        return $this;
    }

    /**
     * @param Property $property Property to add to our class.
     * @return ClassBuilder
     */
    public function addProperty(Property $property) {
        $this->_properties[] = $property;
        return $this;
    }

    /**
     * @return DClass The class object we've been building.
     */
    public function getClass() {
        return new DClass($this->_class, $this->_extends, $this->_implements, $this->_methods, $this->_namespace, $this->_properties);
    }

}