<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Context;

use MooDev\Bounce\Config\Bean;

/**
 * Thing that creates beans.
 */
interface IBeanFactory
{

    /**
     * Create/retrieve an instance of a named bean.
     * @param string $name
     * @return mixed
     */
    public function createByName($name);

    /**
     * @return string[] A map of bean names to class names.
     */
    public function getAllBeanClasses();

    /**
     * Create a bean based on a bean definition.
     * @param Bean $_bean
     * @return mixed
     */
    public function create(Bean $_bean);

}