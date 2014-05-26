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
 * Unsuccessful Api Action exception
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class UnsuccessApiActionException extends \Exception
{
    /**
     * Action request object
     *
     * @var \Msl\RemoteHost\Request\AbstractActionRequest
     */
    public $actionRequest;

    /**
     * Response object
     *
     * @var object
     */
    public $response;

    /**
     * Public class constructor method
     *
     * @param string $message                               the exception message
     * @param \Msl\RemoteHost\Request\AbstractActionRequest $actionRequest the action request object
     * @param object                                        $response      the response object
     */
    public function __construct(
        $message,
        \Msl\RemoteHost\Request\AbstractActionRequest $actionRequest,
        $response
    ) {
        // Calling parent constructor
        parent::__construct($message);

        // Setting object fields
        $this->actionRequest = $actionRequest;
        $this->response      = $response;
    }
}