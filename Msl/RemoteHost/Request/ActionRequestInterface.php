<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\RemoteHost\Request;

/**
 * Action Request Interface
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
interface ActionRequestInterface
{
    /**
     * Initializes a request object
     *
     * @param string $name              the action name
     * @param string $requestType       the request type: class name of the request type object; if no full namespace and class is not found, we use the given name plus the default request namespace plus, eventually, the default request class name suffix.
     * @param string $responseType      the response type: class name of the response type object; if no full namespace and class is not found, we use the given name plus the default response namespace plus, eventually, the default response class name suffix.
     * @param string $responseWrapper   the response wrapper: full class name of the response wrapper object
     * @param string $method            the http method (only POST and GET are supported)
     * @param string $urlBuildMethod    the url build method: constant that indicates how the url request is build (see implementing classes for more details)
     * @param string $baseUrl           the API base url
     * @param string $port              the API port
     * @param array  $parameters        the API call parameters array
     * @param array  $addsOn            the URL adds on array (list of strings to be added to the base url according to their type)
     * @param array  $requestHeaders    the request headers with their default values
     *
     * @return mixed
     */
    public function init(
        $name,
        $requestType,
        $responseType,
        $responseWrapper,
        $method,
        $urlBuildMethod,
        $baseUrl,
        $port,
        array $parameters,
        array $addsOn = array(),
        array $requestHeaders = array()
    );

    /**
     * Configures an action request with the given request values and content
     *
     * @param array  $requestValues      the request parameters
     * @param string $content            the body content
     * @param array  $urlBuildParameters the url build adds on parameter array
     * @param array  $headersValue       the header value array to override default header values
     *
     * @return mixed
     */
    public function configure(array $requestValues, $content = "", array $urlBuildParameters = array(), array $headersValue = array());

    /**
     * Sets a proper EncType on the given \Zend\Http\Client object
     *
     * @param \Zend\Http\Client $client the Zend http client object
     *
     * @return mixed
     */
    public function setClientEncType(\Zend\Http\Client $client);
}