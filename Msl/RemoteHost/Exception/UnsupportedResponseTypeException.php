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