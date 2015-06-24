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
use Msl\RemoteHost\Response\ActionResponseInterface;

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
     * @var string
     */
    protected $requestName;

    /**
     * @var \Zend\Http\Response
     */
    protected $serverRawResponse;

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

    /**
     * Sets the default response values (server raw response, return code, return message and request name)
     *
     * @param ActionResponseInterface $actionResponse
     */
    protected function setDefaultResponseValues(ActionResponseInterface $actionResponse)
    {
        $this->serverRawResponse = $actionResponse->getResponse();
        $this->returnCode        = $this->serverRawResponse->getStatusCode();
        $this->returnMessage     = $this->serverRawResponse->getReasonPhrase();
        $this->requestName       = $actionResponse->getRequestName();
    }

    /**
     * Initializes the object fields with the given raw data
     *
     * @param array                    $rawData        array containing the response raw data
     * @param ActionResponseInterface  $actionResponse the action response object from which to extract additional information
     *
     * @return mixed
     */
    public function init(array $rawData, ActionResponseInterface $actionResponse)
    {
        // Setting raw data field
        $this->rawData = $rawData;

        // Setting response fields
        $this->setDefaultResponseValues($actionResponse);

        // Setting the status
        $this->initStatusFromResponse();
    }


    /**
     * Returns an array containing the data to be used in the hydration process of a given object
     *
     * @return array
     */
    public function getHydrationData()
    {
        return $this->getBody();
    }

    /**
     * Returns true if the Http status code is 200 or 201 or 204; false otherwise
     *
     * @return bool
     */
    public function isHttpStatusSuccessful()
    {
        return $this->serverRawResponse->isSuccess();
    }

    /**
     * Returns true if the http status code is equal to 404; false otherwise
     *
     * @return bool
     */
    public function isNotFoundHttpStatus()
    {
        return $this->serverRawResponse->isNotFound();
    }

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

    /**
     * Sets the server raw response object
     *
     * @param \Zend\Http\Response $serverRawResponse
     */
    public function setServerRawResponse($serverRawResponse)
    {
        $this->serverRawResponse = $serverRawResponse;
    }

    /**
     * Returns the server raw response object
     *
     * @return \Zend\Http\Response
     */
    public function getServerRawResponse()
    {
        return $this->serverRawResponse;
    }

    /**
     * @return string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * @param string $requestName
     */
    public function setRequestName($requestName)
    {
        $this->requestName = $requestName;
    }
}