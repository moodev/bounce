<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Context;

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

}