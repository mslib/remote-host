<?php
/**
 * Unsupported Request Type Exception
 *
 * PHP version 5
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Exception;

/**
 * Unsupported Request Type Exception
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class UnsupportedRequestTypeException extends \Exception
{
    /**
     * The request type
     *
     * @var string
     */
    public $requestType;

    /**
     * Public class constructor method
     *
     * @param string $message     the exception message
     * @param string $requestType the request type
     */
    public function __construct($message, $requestType)
    {
        // Calling parent constructor
        parent::__construct($message);

        // Setting object fields
        $this->requestType = $requestType;
    }
}