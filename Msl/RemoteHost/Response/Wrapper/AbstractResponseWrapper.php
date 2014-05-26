<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\RemoteHost\Response\Wrapper;

/**
 * Abstract Response Wrapper Implementation
 *
 * @category  Response\Wrapper
 * @package   Msl\RemoteHost\Response\Wrapper
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
abstract class AbstractResponseWrapper implements ResponseWrapperInterface
{
    /**
     * The status
     *
     * @var boolean
     */
    protected $status;

    /**
     * The return code
     *
     * @var string
     */
    protected $returnCode;

    /**
     * The return message
     *
     * @var string
     */
    protected $returnMessage;

    /**
     * The response raw data
     *
     * @var mixed
     */
    protected $rawData;

    /*********************************************
     *   S E T T E R S   A N D   G E T T E R S   *
     *********************************************/
    /**
     * Sets the response raw data
     *
     * @param $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * Returns the response raw data
     *
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Sets the return code
     *
     * @param $returnCode
     */
    public function setReturnCode($returnCode)
    {
        $this->returnCode = $returnCode;
    }

    /**
     * Returns the return code
     *
     * @return string
     */
    public function getReturnCode()
    {
        return $this->returnCode;
    }

    /**
     * Sets the return message
     *
     * @param $returnMessage
     */
    public function setReturnMessage($returnMessage)
    {
        $this->returnMessage = $returnMessage;
    }

    /**
     * Returns the return message
     *
     * @return string
     */
    public function getReturnMessage()
    {
        return $this->returnMessage;
    }

    /**
     * Sets the status
     *
     * @param $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns the status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
}