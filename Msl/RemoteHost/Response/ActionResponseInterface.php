<?php
/**
 * Action Response Interface
 *
 * PHP version 5
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
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
}