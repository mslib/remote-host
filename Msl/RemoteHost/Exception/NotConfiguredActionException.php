<?php
/**
 * Not configured action exception
 *
 * PHP version 5
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Exception;

/**
 * Not configured action exception
 *
 * @category  Exception
 * @package   Msl\RemoteHost\Exception
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class NotConfiguredActionException extends \Exception
{
    /**
     * The non-configured action name
     *
     * @var string
     */
    public $actionName;

    /**
     * Public class constructor method
     *
     * @param string $message    the exception message
     * @param string $actionName the action name
     */
    public function __construct($message, $actionName)
    {
        // Calling parent constructor
        parent::__construct($message);

        // Setting object fields
        $this->actionName = $actionName;
    }
}