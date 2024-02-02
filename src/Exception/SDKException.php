<?php

namespace Chainstarters\Exception;

/**
 * Class SDKException
 *
 * Custom exception class for Chainstarters SDK.
 */
class SDKException extends \Exception
{
    /**
     * Construct the exception.
     *
     * @param string $message The Exception message to throw.
     * @param int $code The Exception code.
     * @param \Throwable|null $previous The previous throwable used for exception chaining.
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * String representation of the exception.
     *
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
    
    // Additional custom methods specific to your SDK can be added here
}
