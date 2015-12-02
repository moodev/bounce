<?php
/**
 * Created by IntelliJ IDEA.
 * User: jono
 * Date: 02/12/2015
 * Time: 11:33
 */

namespace MooDev\Bounce\Context;


interface IBeanFactory
{

    public function createByName($name);

    public function getAllBeanClasses();

}