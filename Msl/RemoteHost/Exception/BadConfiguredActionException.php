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
 * Not configured action exception
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class BadConfiguredActionException extends \Exception
{
    /**
     * Action request object
     *
     * @var \Msl\RemoteHost\Request\AbstractActionRequest
     */
    public $actionRequest;

    /**
     * Public class constructor method
     *
     * @param string                                        $message       the exception message
     * @param \Msl\RemoteHost\Request\AbstractActionRequest $actionRequest the action request object
     */
    public function __construct($message, \Msl\RemoteHost\Request\AbstractActionRequest $actionRequest)
    {
        // Calling parent constructor
        parent::__construct($message);

        // Setting object fields
        $this->actionRequest = $actionRequest;
    }
}