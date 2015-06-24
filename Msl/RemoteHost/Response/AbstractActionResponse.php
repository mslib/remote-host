<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\RemoteHost\Response;

/**
 * Abstract Action Response Class
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
abstract class AbstractActionResponse implements ActionResponseInterface
{
    /**
     * @var string
     */
    protected $requestName;

    /**
     * Response wrapper instance
     *
     * @var Wrapper\ResponseWrapperInterface
     */
    protected $responseWrapper;

    /**
     * Response object
     *
     * @var \Zend\Http\Response
     */
    protected $response;

    /*********************************************
     *   S E T T E R S   A N D   G E T T E R S   *
     *********************************************/
    /**
     * Sets a Response object
     *
     * @param \Zend\Http\Response $response the http response object
     *
     * @return mixed|void
     */
    public function setResponse(\Zend\Http\Response $response)
    {
        $this->response = $response;
    }

    /**
     * Returns a Response object
     *
     * @return \Zend\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets an instance of ResponseWrapperInterface
     *
     * @param Wrapper\ResponseWrapperInterface $responseWrapperInterface the response wrapper instance
     */
    public function setResponseWrapper(Wrapper\ResponseWrapperInterface $responseWrapperInterface)
    {
        $this->responseWrapper = $responseWrapperInterface;
    }

    /**
     * Returns an instance of ResponseWrapperInterface
     *
     * @return Wrapper\ResponseWrapperInterface
     */
    public function getResponseWrapper()
    {
        return $this->responseWrapper;
    }

    /**
     * Returns a ResponseWrapperInterface instance
     *
     * @return Wrapper\ResponseWrapperInterface
     */
    public function getParsedResponse()
    {
        // We first get an array representing the response
        $rawData = $this->bodyToArray();

        // If we have a wrapper response object initialized in the current ActionResponse object, then we return it.
        if ($this->responseWrapper instanceof Wrapper\ResponseWrapperInterface) {
            $this->responseWrapper->init($rawData, $this);
            return $this->responseWrapper;
        }
        return $rawData;
    }

    /**
     * Returns the request name (name or uri) for this action response object
     *
     * @return string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * Sets the request name (name or uri) for this action response object
     *
     * @param string $requestName
     *
     * @return mixed
     */
    public function setRequestName($requestName)
    {
        $this->requestName = $requestName;
    }
}