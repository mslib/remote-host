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

use Zend\Http\Request;
use Zend\Uri\Http;
use Msl\RemoteHost\Exception;

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
     * URL Build Methods
     */
    const PLAIN_URL_BUILD_METHOD    = 0;
    const ADDS_ON_URL_BUILD_METHOD  = 1;

    /**
     * URL Adds-On constants
     */
    const REPLACE_TYPE_ADD_ON    = 'replace';
    const PLAIN_TEXT_TYPE_ADD_ON = 'plain';

    /**
     * URL Adds-On access key constants
     */
    const TYPE_ADD_ON                    = 'type';
    const CONTENT_ADD_ON                 = 'content';
    const REPLACE_DELIMITER_OPEN_ADD_ON  = '{{';
    const REPLACE_DELIMITER_CLOSE_ADD_ON = '}}';

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

    /**
     * True if the url has been built during the configuration process (no replace adds on); false if it has to be built at execution time
     *
     * @var bool
     */
    protected $urlBuilt = false;

    /**
     * List of url adds on (these strings will be added to the base url according to the specified add-on type)
     *
     * @var array
     */
    protected $addsOn;

    /**
     * List of headers to be set in the request object
     *
     * @var array
     */
    protected $requestHeaders;

    /*************************************************
     *   C O N F I G U R A T I O N   M E T H O D S   *
     *************************************************/
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
    ) {
        // Setting object fields
        $this->name             = $name;
        $this->requestType      = $requestType;
        $this->responseType     = $responseType;
        $this->responseWrapper  = $responseWrapper;
        $this->uriBuildMethod   = $urlBuildMethod;
        $this->parameters       = $parameters;
        $this->baseUrl          = rtrim($baseUrl, '/');
        $this->port             = $port;
        $this->addsOn           = $addsOn;
        $this->requestHeaders   = $requestHeaders;

        // Configuring parent object field
        $this->setMethod($method);
        $this->setPort($this->port);
    }

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
    public function configure(array $requestValues, $content = "", array $urlBuildParameters = array(), array $headersValue = array())
    {
        // Setting request Uri object
        $this->setRequestUri($urlBuildParameters);

        // Setting headers from header array
        if (count($headersValue) > 0) {
            #$this->getHeaders()->addHeaders($headersValue);
        }
    }

    /**
     * Sets the Request Uri object
     *
     * @param array $parameters Url construction parameter array
     *
     * @return void
     */
    protected function setRequestUri(array $parameters = array())
    {
        // Getting base uri according to the uri construction method
        $uri = $this->constructActionUrl($parameters);

        // Converting it into a Zend Http Uri object
        $httpUri = new Http($uri);
        $httpUri->setPort($this->port);

        // Setting request uri object
        $this->setUri($httpUri);
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
     * Sets the UrlBuilt flag value
     *
     * @param boolean $urlBuilt
     */
    public function setUrlBuilt($urlBuilt)
    {
        $this->urlBuilt = $urlBuilt;
    }

    /**
     * Returns the UrlBuilt flag value
     *
     * @return boolean
     */
    public function getUrlBuilt()
    {
        return $this->urlBuilt;
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

        // Returning populated array
        return $parameters;
    }

    /**
     * Returns the url of the remote api action to be called
     *
     * @param array $parameters the url construction parameter array
     *
     * @throws \Msl\RemoteHost\Exception\BadConfiguredActionException
     *
     * @return string
     */
    protected function constructActionUrl(array $parameters = array())
    {
        switch($this->uriBuildMethod) {
            case self::PLAIN_URL_BUILD_METHOD;
                return $this->baseUrl;
                break;
            case self::ADDS_ON_URL_BUILD_METHOD;
                return $this->replaceAddsOn($this->baseUrl, $parameters);
                break;
            default;
                throw new Exception\BadConfiguredActionException(
                    sprintf('Unknown url build method: %s', $this->uriBuildMethod),
                    $this
                );
                break;
        }
    }

    /**
     * Adds all adds on the given url string (each add on will be treated individually according to its type)
     *
     * @param string $url        the url to which all adds on will be added
     * @param array  $parameters the parameter array containing all values to be replaced in replace adds on
     *
     * @return string
     *
     * @throws \Msl\RemoteHost\Exception\BadRequestAddOnConfiguredException
     */
    protected function replaceAddsOn($url, array $parameters = array())
    {
        // Parsing the list of adds on and treating them individually according to their type
        $urlAddsOnToString = '';
        foreach ($this->addsOn as $addOn) {
            if (isset($addOn[self::TYPE_ADD_ON]) && isset($addOn[self::CONTENT_ADD_ON])) {
                // Checking add-on type
                $type    = $addOn[self::TYPE_ADD_ON];
                $content = $addOn[self::CONTENT_ADD_ON];
                switch($type) {
                    case self::REPLACE_TYPE_ADD_ON;
                        $addOnContent = $content;
                        foreach ($parameters as $name => $value) {
                            // for each parameter we replace its value in the replace adds on and then we add it to the url
                            $searchIndex = self::REPLACE_DELIMITER_OPEN_ADD_ON . $name . self::REPLACE_DELIMITER_CLOSE_ADD_ON;
                            $addOnContent = str_replace($searchIndex, $value, $addOnContent);
                        }
                        if (strpos($addOnContent, self::REPLACE_DELIMITER_OPEN_ADD_ON) !== false
                            || strpos($addOnContent, self::REPLACE_DELIMITER_CLOSE_ADD_ON) !== false
                        ) {
                            throw new Exception\BadRequestAddOnConfiguredException(
                                sprintf('Missing replace value for the replace add-on: \'%s\'', $content)
                            );
                        }
                        // adding the current value to the stringified add on
                        if (!empty($addOnContent)) {
                            $urlAddsOnToString = trim($urlAddsOnToString, '/') . '/' . $addOnContent;
                        }
                        break;
                    case self::PLAIN_TEXT_TYPE_ADD_ON;
                        $urlAddsOnToString = trim($urlAddsOnToString, '/') . '/' . $content;
                        break;
                    default;
                        throw new Exception\BadRequestAddOnConfiguredException(
                            sprintf('Unknown add-on type: \'%s\'', $type)
                        );
                        break;
                }
            } else {
                throw new Exception\BadRequestAddOnConfiguredException(
                    sprintf(
                        'Bad configured add-on. Each add-on must specify the following parameters: \'%s\' and \'%s\'',
                        self::TYPE_ADD_ON,
                        self::CONTENT_ADD_ON
                    )
                );
            }
        }
        // Adding the adds on string to the url if not empty
        if (!empty($urlAddsOnToString)) {
            $url = trim($url, '/') . '/' . trim($urlAddsOnToString, '/');
        }
        return $url;
    }
}