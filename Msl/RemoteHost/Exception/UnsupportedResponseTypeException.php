<?php
/**
 * Unsupported Response Type Exception
 *
 * PHP version 5
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Exception;

/**
 * Unsupported Response Type Exception
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class UnsupportedResponseTypeException extends \Exception
{
    /**
     * The response type
     *
     * @var string
     */
    public $responseType;

    /**
     * Public class constructor method
     *
     * @param string $message      the exception message
     * @param string $responseType the response type
     */
    public function __construct($message, $responseType)
    {
        // Calling parent constructor
        parent::__construct($message);

        // Setting object fields
        $this->responseType = $responseType;
    }
}