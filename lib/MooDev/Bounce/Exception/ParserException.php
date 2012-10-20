<?php
namespace MooDev\Bounce\Exception;
use Exception;

/**
 * Denotes that there was a problem while parsing XML.
 */
class ParserException extends Exception
{

    /**
     * Creates a new ParserException instance
     *
     * @param string $message a message detailing the nature of the parsing
     *                        problem.
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
