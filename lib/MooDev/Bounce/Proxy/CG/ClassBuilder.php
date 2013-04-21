<?php


namespace MooDev\Bounce\Proxy\CG;


class ClassBuilder {

    private $_class = null;
    private $_namespace = null;
    private $_properties = array();
    private $_methods = array();
    private $_implements = array();
    private $_extends = array();

    public static function build($name, $namespace = null) {
        return new ClassBuilder($name, $namespace);
    }

    public function __construct($name, $namespace = null) {
        $this->_namespace = $namespace;
        $this->_class = $name;
    }

    /**
     * @param $class
     * @return ClassBuilder
     */
    public function extend($class) {
        $this->_extends[] = $class;
        return $this;
    }

    /**
     * @param $interface
     * @return ClassBuilder
     */
    public function implement($interface) {
        $this->_implements[] = $interface;
        return $this;
    }

    /**
     * @param Method $method
     * @return ClassBuilder
     */
    public function addMethod(Method $method) {
        $this->_methods[] = $method;
        return $this;
    }

    /**
     * @param Property $property
     * @return ClassBuilder
     */
    public function addProperty(Property $property) {
        $this->_properties[] = $property;
        return $this;
    }

    public function getClass() {
        return new DClass($this->_class, $this->_extends, $this->_implements, $this->_methods, $this->_namespace, $this->_properties);
    }

}