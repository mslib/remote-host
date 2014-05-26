<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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