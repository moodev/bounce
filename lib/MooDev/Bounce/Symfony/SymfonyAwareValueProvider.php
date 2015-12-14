<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2015, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Symfony;


use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * A value provider that returns Symfony Definitions/References.
 * These are useless to vanilla bounce (you need to implement the normal ValueProvider interface for that.)
 * You can implement both interfaces, in which case the Symfony implementations will prefer this interface
 * over the normal ValueProvider.
 */
interface SymfonyAwareValueProvider
{
    /***
     * @return mixed|Definition|Reference
     */
    public function getSymfonyValue();
}