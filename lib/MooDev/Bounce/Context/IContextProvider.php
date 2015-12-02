<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Context;

use MooDev\Bounce\Config;
use MooDev\Bounce\Exception\BounceException;

/**
 * Provider of bounce config contexts
 */
interface IContextProvider
{

    /**
     * @return Config\Context
     * @throws BounceException
     */
    public function getContext();

}