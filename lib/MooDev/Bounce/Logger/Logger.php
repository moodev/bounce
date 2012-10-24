<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, MOO Print Ltd.
 * @license ISC
 */
namespace MooDev\Bounce\Logger;

/**
 * In the moo codebase we use something descended from Zend_Log. To avoid you needing to meet that dependency
 * this annoying interface exists so that my IDE stops being angry about the lack of an interface.
 */
interface Logger
{
    public function info($msg);
    public function debug($msg);
    public function warn($msg);
    public function emerg($msg);
    
    /**
     * @return bool whether the debug level is enabled, so you don't expensively build debug messages
     * which will be thrown away.
     */
    public function isDebugEnabled();

    /**
     * @return bool whether the info level is enabled, so you don't expensively build messages
     * which will be thrown away.
     */
    public function isInfoEnabled();

    /**
     * @return bool whether the warn level is enabled, so you don't expensively build warnings
     * which will be thrown away.
     */
    public function isWarnEnabled();
}
