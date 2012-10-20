<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace MooDev;

/**
 * @param string $name Class to load
 * @return void
 */
function bounceAutoloader($name)
{
    if (class_exists($name) || interface_exists($name)) {
        return;
    }
    $exploded = explode("\\", $name);
    if (count($exploded) < 3) {
        return;
    }
    $vendor = $exploded[0];
    $project = $exploded[1];
    if ($vendor != "MooDev" || $project != "Bounce") {
        return;
    }
    $path = implode('/', $exploded);
    /** @noinspection PhpIncludeInspection */
    include(__DIR__ . '/' . $path . '.php');
    return;
}

spl_autoload_register('\MooDev\bounceAutoloader');

