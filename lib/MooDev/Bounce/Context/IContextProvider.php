<?php
/**
 * Created by IntelliJ IDEA.
 * User: jono
 * Date: 02/12/2015
 * Time: 11:37
 */

namespace MooDev\Bounce\Context;

use MooDev\Bounce\Config;
use MooDev\Bounce\Exception\BounceException;

interface IContextProvider
{

    /**
     * @return Config\Context
     * @throws BounceException
     */
    public function getContext();

}