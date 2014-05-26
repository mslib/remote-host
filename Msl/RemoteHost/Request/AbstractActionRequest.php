<?php
/**
 * Abstract Action Request Object: extension of Zend\Http\Request
 *
 * PHP version 5
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Request;

use Zend\Http\Request;
use Msl\RemoteHost\Exception\BadApiConfigurationException;

/**
 * Abstract Action Request Object: extension of Zend\Http\Request
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
abstract class AbstractActionRequest extends Request implements ActionRequestInterface
{
    /**
     * The action name will be concatenated to the base url
     *
     * @const integer
     */
    const ACTION_NAME_AS_URI_PART = 1;

    /**
     * The action name will not be concatenated to the base url
     *
     * @const integer
     */
    const ACTION_NAME_NOT_USED = 2;

    /**
     * Request objects base namespace
     *
     * @const string
     */
    const REQUEST_NAMESPACE = '\Msl\RemoteHost\Request';

    /**
     * Request objects base namespace
     *
     * @const string
     */
    const REQUEST_CLASSNAME_SUFFIX = 'ActionRequest';

    /**
     * Response objects base namespace
     *
     * @const string
     */
    const RESPONSE_NAMESPACE = '\Msl\RemoteHost\Response';

    /**
     * Response objects base namespace
     *
     * @const string
     */
    const RESPONSE_CLASSNAME_SUFFIX = 'ActionResponse';

    /**
     * The name of the action to be called as specified by the company owner of the api method to be called
     *
     * @var string
     */
    protected $name;

    /**
     * The type of request (json, xml, text, etc.)
     *
     * @var string
     */
    protected $requestType;

    /**
     * The type of response (json, xml, text, etc.)
     *
     * @var string
     */
    protected $responseType;

    /**
     * Full namespace of the wrapper class for the request response
     *
     * @var string
     */
    protected $responseWrapper;

    /**
     * How the api call uri is built
     *
     * @var mixed
     */
    protected $uriBuildMethod;

    /**
     * Array containing all required action parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * Connection port
     *
     * @var string
     */
    protected $port;

    /**
     * Base connection url
     *
     * @var string
     */
    protected $baseUrl;

    /*************************************************
     *   C O N F I G U R A T I O N   M E T H O D S   *
     *************************************************/
    /**
     * Initializes a request object
     *
     * @param string $name            the action name
     * @param string $requestType     the request type: class name of the request type object; if no full namespace and class is not found, we use the given name plus the default request namespace plus, eventually, the default request class name suffix.
     * @param string $responseType    the response type: class name of the response type object; if no full namespace and class is not found, we use the given name plus the default response namespace plus, eventually, the default response class name suffix.
     * @param string $responseWrapper the response wrapper: full class name of the response wrapper object
     * @param string $method          the http method (only POST and GET are supported)
     * @param string $urlBuildMethod  the url build method: constant that indicates how the url request is build (see implementing classes for more details)
     * @param string $baseUrl         the API base url
     * @param string $port            the API port
     * @param array  $parameters      the API call parameters array
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
        array $requiredParameters
    ) {
        // Setting object fields
        $this->name             = $name;
        $this->requestType      = $requestType;
        $this->responseType     = $responseType;
        $this->responseWrapper  = $responseWrapper;
        $this->uriBuildMethod   = $urlBuildMethod;
        $this->parameters       = $requiredParameters;
        $this->baseUrl          = rtrim($baseUrl, '/');
        $this->port             = $port;

        // Configuring parent object field
        $this->setMethod($method);
        $this->setPort($this->port);

        // Setting Request Uri and checking if it is valid
        $this->setRequestUri();
    }

    /**
     * Configures an action request with the given request values and content
     *
     * @param array  $requestValues   the request parameters
     * @param string $content         the body content
     * @param bool   $trimRequestName remove or not final '/' from action name or not
     *
     * @return mixed
     */
    public function configure(array $requestValues, $content = "", $trimRequestName = true)
    {
        if ($trimRequestName) {
            $this->trimActionName();
        }
    }

    /*********************************************
     *   S E T T E R S   A N D   G E T T E R S   *
     *********************************************/
    /**
     * Sets the base url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * Returns the base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Sets the action name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the action name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the port
     *
     * @param string $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Returns the port
     *
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the request type
     *
     * @param string $requestType
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;
    }

    /**
     * Returns the request type
     *
     * @return string
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Sets the parameters array
     *
     * @param array $requiredParameters
     */
    public function setParameters($requiredParameters)
    {
        $this->parameters = $requiredParameters;
    }

    /**
     * Returns the parameters array
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the response type
     *
     * @param string $responseType
     */
    public function setResponseType($responseType)
    {
        $this->responseType = $responseType;
    }

    /**
     * Returns the response type
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Sets the uri build method
     *
     * @param mixed $uriBuildMethod
     */
    public function setUriBuildMethod($uriBuildMethod)
    {
        $this->uriBuildMethod = $uriBuildMethod;
    }

    /**
     * Returns the uri build method
     *
     * @return mixed
     */
    public function getUriBuildMethod()
    {
        return $this->uriBuildMethod;
    }

    /**
     * Sets the response wrapper
     *
     * @param string $responseWrapper
     */
    public function setResponseWrapper($responseWrapper)
    {
        $this->responseWrapper = $responseWrapper;
    }

    /**
     * Returns the response wrapper
     *
     * @return string
     */
    public function getResponseWrapper()
    {
        return $this->responseWrapper;
    }


    /*************************************
     *   G E N E R A L   M E T H O D S   *
     *************************************/
    /**
     * Returns a string representation of the current object
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '[ActionRequest] name: %s | request: %s | response: %s | url: %s | port: %s | method: %s | build : %s',
            $this->name,
            $this->responseType,
            $this->requestType,
            $this->baseUrl,
            $this->port,
            $this->method,
            $this->uriBuildMethod
        );
    }

    /**
     * Removes any ending '/' from the action name
     *
     * @return void
     */
    public function trimActionName()
    {
        $this->name = trim($this->name, '/');
    }

    /**
     * Returns an array of all parameters with a given value
     *
     * @param $requestValues
     *
     * @return array
     */
    public function getParametersWithValue($requestValues)
    {
        // Final request parameters array
        $parameters = array();

        // Setting parameters
        foreach ($this->parameters as $paramKey => $paramDefaultValue) {
            // Overriding parameter value with the given one
            if (isset($requestValues[$paramKey]) && !empty($requestValues[$paramKey])) {
                $parameters[$paramKey] = $requestValues[$paramKey];
            } elseif (!empty($paramDefaultValue)) {
                $parameters[$paramKey] = $paramDefaultValue;
            }
        }

        // Returing populated array
        return $parameters;
    }

    /**
     * Returns the url of the remote api action to be called
     *
     * @return string
     *
     * @throws BadApiConfigurationException
     */
    protected function constructActionUri()
    {
        switch($this->uriBuildMethod) {
            case self::ACTION_NAME_AS_URI_PART;
                return rtrim($this->baseUrl, '/') . '/' . $this->name;
                break;
            case self::ACTION_NAME_NOT_USED;
                return $this->baseUrl;
                break;
            default;
                throw new BadApiConfigurationException(sprintf('Unknown url build method: %s', $this->uriBuildMethod));
                break;
        }
    }

    /**
     * Sets the Request Uri object
     *
     * @return void
     *
     * @throws BadApiConfigurationException
     */
    protected function setRequestUri()
    {
        // Getting base uri according to the uri construction method
        $uri = $this->constructActionUri();

        // Converting it into a Zend Http Uri object
        $httpUri = new \Zend\Uri\Http($uri);
        $httpUri->setPort($this->port);

        // Setting request uri object
        $this->setUri($httpUri);
    }
}