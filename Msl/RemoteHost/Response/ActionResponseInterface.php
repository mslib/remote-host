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
 * Action Response Interface
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
interface ActionResponseInterface
{
    /**
     * Sets a Response object
     *
     * @param \Zend\Http\Response $response the http response object
     *
     * @return mixed
     */
    public function setResponse(\Zend\Http\Response $response);

    /**
     * Returns a Response object
     *
     * @return \Zend\Http\Response
     */
    public function getResponse();

    /**
     * Sets a ResponseWrapperInterface implementation to the ActionResponseInterface implementation
     *
     * @param Wrapper\ResponseWrapperInterface $responseWrapper the response wrapper object
     *
     * @return mixed
     */
    public function setResponseWrapper(Wrapper\ResponseWrapperInterface $responseWrapper);

    /**
     * Converts the Response object body to an array
     *
     * @return array
     */
    public function bodyToArray();

    /**
     * Returns a ResponseWrapperInterface instance
     *
     * @return Wrapper\ResponseWrapperInterface
     */
    public function getParsedResponse();

    /**
     * Returns the request name (name or uri) for this action response object
     *
     * @return string
     */
    public function getRequestName();

    /**
     * Sets the request name (name or uri) for this action response object
     *
     * @param string $requestName
     *
     * @return mixed
     */
    public function setRequestName($requestName);
}