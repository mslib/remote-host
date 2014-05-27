<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\RemoteHost\Api;

use Zend\Http\Client;
use Msl\RemoteHost\Exception\BadApiConfigurationException;
use Msl\RemoteHost\Exception\BadConfiguredActionException;
use Msl\RemoteHost\Exception\NotConfiguredActionException;
use Msl\RemoteHost\Exception\UnsuccessApiActionException;
use Msl\RemoteHost\Exception\UnsupportedResponseTypeException;
use Msl\RemoteHost\Exception\UnsupportedRequestTypeException;
use Msl\RemoteHost\Request\ActionRequestInterface;
use Msl\RemoteHost\Request\AbstractActionRequest;
use Msl\RemoteHost\Response\ResponseWrapper;
use Msl\RemoteHost\Response\JsonResponse;
use Msl\RemoteHost\Response\XmlResponse;
use Msl\RemoteHost\Response\PlainTextResponse;
use Msl\RemoteHost\Response\ActionResponseInterface;
use Msl\RemoteHost\Response\Wrapper\DefaultResponseWrapper;

/**
 * Abstract host api
 *
 * @category  Api
 * @package   Msl\RemoteHost\Api
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
abstract class AbstractHostApi implements HostApiInterface
{
    /**
     * String containing the name of this api. This value will be used mainly for log purposes
     *
     * @const string
     */
    const API_NAME = 'HOST_API';

    /**
     * Separator character for all action names. E.g. customer.address
     *
     * @const string
     */
    const API_NAME_LEVEL_SEPARATOR = '.';

    /**
     * API Name
     *
     * @var string
     */
    protected $apiName;

    /**
     * API Hostname
     *
     * @var string
     */
    protected $host;

    /**
     * API Port
     *
     * @var int
     */
    protected $port;

    /**
     * API User
     *
     * @var string
     */
    protected $user;

    /**
     * API Password
     *
     * @var string
     */
    protected $password;

    /**
     * Zend Http Client to connect to the remote API
     *
     * @var Client
     */
    protected $client;

    /**
     * Collection of \Msl\RemoteHost\Request\ActionRequestInterface objects
     *
     * @var array
     */
    protected $actions;

    /**
     * Action configuration array
     *
     * @var array
     */
    protected $actionsConfig;

    /**
     * Action common configuration array
     *
     * @var array
     */
    protected $actionsCommonConfig;

    /**
     * Init all actions or not
     *
     * @var bool
     */
    protected $initAllActions = false;

    /*****************************
     *   C O N S T R U C T O R   *
     *****************************/
    /**
     * Class constructor.
     *
     * If no config array, the default one will be used (see method getDefaultConfig).
     * If no api name specified, the default one will be used (see constant API_NAME).
     *
     * @param string $apiName the api name (used in exception and logs)
     * @param array  $config  the action configuration array
     */
    public function __construct($apiName = null, $config = null)
    {
        // Setting api name
        if (!empty($apiName)) {
            $this->apiName = $apiName;
        } else {
            $this->apiName = static::API_NAME;
        }

        // Setting actions configuration
        if (!is_array($config)) {
            $config = $this->getDefaultConfig();
        }
        $this->setConfiguration($config);
    }

    /*************************************************
     *   C O N F I G U R A T I O N   M E T H O D S   *
     *************************************************/
    /**
     * Returns an array containing the default configuration
     *
     * @return array
     */
    abstract public function getDefaultConfig();

    /**
     * Sets the configuration
     *
     * @param array $configuration the configuration for this host api instance
     *
     * @return mixed|void
     *
     * @throws \Msl\RemoteHost\Exception\BadApiConfigurationException
     */
    public function setConfiguration(array $configuration)
    {
        // Setting action requests array
        $this->actions = array();

        // Setting Http client object
        $this->client = new Client();

        // Checking if configuration matrix contains a parameters array
        if (!isset($configuration['parameters'])) {
            throw new BadApiConfigurationException(sprintf(
                    '[%s] Missing parameters array for api configuration', $this->getApiName())
            );
        } else {
            // Setting general api call parameters
            $this->setGeneralApiCallParameters($configuration['parameters']);
        }

        // Getting common actions parameters
        $actionConfiguration = array();
        if (isset($configuration['actions_parameters'])) {
            $actionConfiguration = $configuration['actions_parameters'];
        }

        // Checking if configuration matrix contains an actions array
        if (!isset($configuration['actions'])) {
            throw new BadApiConfigurationException(sprintf(
                '[%s] Missing actions array for api configuration', $this->getApiName())
            );
        } else {
            if ($this->getInitAllActions()) {
                // Setting api call actions (this method should always be defined in the child class)
                $this->setActionRequests($configuration['actions'], $actionConfiguration);
            } else {
                $this->actionsCommonConfig = $actionConfiguration;
                $this->actionsConfig       = $configuration['actions'];
            }
        }

        // Configuring client
        if (isset($configuration['config'])) {
            $configs = $configuration['config'];
            if (is_array($configs)) {
                $this->configureClientConfig($configs);
            }
        }
    }

    /**
     * Merges two action parameter configuration array using a priority criteria
     *
     * @param array $lowPriorityConfig  low priority config array
     * @param array $highPriorityConfig high priority config array
     *
     * @return void
     */
    public function mergeActionParameterConfigs(array &$lowPriorityConfig, array &$highPriorityConfig)
    {
        if (isset($lowPriorityConfig['actions_parameters']) && isset($highPriorityConfig['actions_parameters'])) {
            $lowPriorityConfig['actions_parameters'] =
                $this->mergeConfigs($lowPriorityConfig['actions_parameters'], $highPriorityConfig['actions_parameters']);
        }
    }

    /**
     * Merges two action parameter configuration array using a priority criteria (empty values are ignored)
     *
     * @param array $lowPriorityConfig  low priority config array
     * @param array $highPriorityConfig high priority config array
     *
     * @return array
     */
    public function mergeConfigs(array $lowPriorityConfig, array $highPriorityConfig)
    {
        foreach ($highPriorityConfig as $key => $value) {
            if ($highPriorityConfig[$key] !== '') {
                $lowPriorityConfig[$key] = $highPriorityConfig[$key];
            }
        }
        return $lowPriorityConfig;
    }

    /**
     * Configures the client configs
     *
     * @param array $configs the http client configuration array
     *
     * @return mixed
     */
    public function configureClientConfig(array $configs)
    {
        // We set the http client object
        $this->client->setOptions($configs);

        // We return the configured http client object
        return $this->client;
    }

    /**
     * Sets all general parameters for all api calls
     *
     * @param array $apiCallParameters the api call parameters array
     *
     * @return void
     *
     * @throws \Msl\RemoteHost\Exception\BadApiConfigurationException
     */
    public function setGeneralApiCallParameters(array $apiCallParameters)
    {
        if (!isset($apiCallParameters['host'])) {
            throw new BadApiConfigurationException(sprintf(
                '[%s] Missing parameter for GENERAL API configuration: host.', $this->getApiName())
            );
        }

        // Setting api call parameters
        $this->host = $apiCallParameters['host'];

        // Setting port
        if (isset($apiCallParameters['port'])) {
            $this->port = $apiCallParameters['port'];
        }

        // Setting user
        if (isset($apiCallParameters['user'])) {
            $this->user = $apiCallParameters['user'];
        }

        // Setting password
        if (isset($apiCallParameters['password'])) {
            $this->password = $apiCallParameters['password'];
        }

        // If defined, we set user and password parameters for authentication purposes
        if ($this->user && $this->password) {
            $this->client->setAuth($this->user, $this->password);
        }
    }

    /**
     * Default method to set the remote actions to be called
     *
     * @param array $actionsParameters        actions parameters array
     * @param array $defaultActionsParameters default action parameters array
     *
     * @return void
     *
     * @throws \Msl\RemoteHost\Exception\BadApiConfigurationException
     * @throws \Msl\RemoteHost\Exception\NotConfiguredActionException
     */
    public function setActionRequests(array $actionsParameters, array $defaultActionsParameters = array())
    {
        // We currently support two levels of action name (e.g. )
        foreach ($actionsParameters as $actionNameFirstLevel => $actionGroup) {
            // We access the second level of action name and we configure a new ActionRequestInterface instance.
            foreach ($actionGroup as $actionNameSecondLevel => $actionConf) {
                $this->setActionRequest($actionConf, $actionNameFirstLevel, $actionNameSecondLevel, $defaultActionsParameters);
            }
        }
    }

    /**
     * Default method to set a single remote action object
     *
     * @param array     $actionConf                 the action configuration array
     * @param array     $actionNameFirstLevel       the first part of the action name
     * @param string    $actionNameSecondLevel      the second part of the action name
     * @param array     $defaultActionsParameters   the default parameters for the current action
     *
     * @return void
     *
     * @throws \Msl\RemoteHost\Exception\BadApiConfigurationException
     * @throws \Msl\RemoteHost\Exception\NotConfiguredActionException
     */
    protected function setActionRequest(
        $actionConf,
        $actionNameFirstLevel,
        $actionNameSecondLevel,
        $defaultActionsParameters
    ) {
        /*****************************
         * GENERAL ACTION PARAMETERS *
         *****************************/
        // Checking if action name configuration part is well built
        if (!isset($actionConf['name'])) {
            throw new BadApiConfigurationException(sprintf(
                '[%s] Missing parameter for action configuration: name.', $this->getApiName())
            );
        }
        $name = $actionConf['name'];

        /******************************
         * GENERAL REQUEST PARAMETERS *
         ******************************/
        // Checking if action request configuration part is well built
        if (!isset($actionConf['request'])) {
            throw new BadApiConfigurationException(sprintf(
                '[%s] Missing parameter for action configuration: request.', $this->getApiName())
            );
        }
        $request = $actionConf['request'];

        // Checking if action request type configuration part is well built
        if (!isset($request['type'])) {
            throw new BadApiConfigurationException(sprintf(
                '[%s] Missing parameter for action configuration: request->type.', $this->getApiName())
            );
        }
        $requestType = $request['type'];

        // Checking adds_on configuration parameter
        $addsOn = array();
        if (isset($request['adds_on']) && is_array($request['adds_on'])) {
            $addsOn = $request['adds_on'];
        }

        // Setting url build method according to the presence or not of adds on
        if (count($addsOn) > 0) {
            $urlBuildMethod = AbstractActionRequest::ADDS_ON_URL_BUILD_METHOD;
        } else {
            $urlBuildMethod = AbstractActionRequest::PLAIN_URL_BUILD_METHOD;
        }

        // Request method is by default HTTP POST
        $method = AbstractActionRequest::METHOD_POST;
        if (isset($request['method'])) {
            $method = $request['method'];
        }

        // Now getting request parameters: default value is an empty array
        $parameters = array();
        if (isset($request['parameters'])) {
            $parameters = $request['parameters'];

            // Checking default parameters: request parameters are reste only if empty or not defined
            foreach ($defaultActionsParameters as $key => $value) {
                if (!isset($parameters[$key]) || (isset($parameters[$key]) && $parameters[$key] === '')) {
                    $parameters[$key] = $value;
                }
            }
        }

        /*******************
         * HOST PARAMETERS *
         *******************/
        // Overriding general api call parameters if defined
        $host = $this->host;
        if (isset($request['host']) && !empty($request['host'])) {
            $host = $request['host'];
        }
        $port = $this->port;
        if (isset($request['port']) && !empty($request['port'])) {
            $port = $request['port'];
        }

        /*******************************
         * GENERAL RESPONSE PARAMETERS *
         *******************************/
        // Checking if action response configuration part is well built
        if (!isset($actionConf['response'])) {
            throw new BadApiConfigurationException(sprintf(
                '[%s] Missing parameter for action configuration: response.', $this->getApiName())
            );
        }
        $response = $actionConf['response'];

        // Checking if action response type configuration part is well built
        if (!isset($response['type'])) {
            throw new BadApiConfigurationException(sprintf(
                '[%s] Missing parameter for action configuration: response->type.', $this->getApiName())
            );
        }
        $responseType = $response['type'];

        // Checking if action response wrapper configuration part is configured
        $responseWrapper = null;
        if (isset($response['wrapper']) && is_string($response['wrapper'])) {
            $responseWrapper = $response['wrapper'];
        }

        /************************************
         * GENERATING ACTION REQUEST OBJECT *
         ************************************/
        try {
            // Getting an instance of ActionRequestInterface
            $actionRequestObj = $this->getRequestInstance($requestType);
            // Initializing action object and adding it to the collection
            $actionRequestObj->init(
                $name,
                $requestType,
                $responseType,
                $responseWrapper,
                $method,
                $urlBuildMethod,
                $host,
                $port,
                $parameters,
                $addsOn
            );
            $this->actions[$actionNameFirstLevel][$actionNameSecondLevel] = $actionRequestObj;
        } catch (BadApiConfigurationException $bace) {
            $fullActionName = $actionNameFirstLevel . self::API_NAME_LEVEL_SEPARATOR . $actionNameSecondLevel;
            throw new NotConfiguredActionException(
                sprintf(
                    '[%s] Error while creating an ActionRequestInterface instance for the action \'%s\': \'%s\'.',
                    $this->getApiName(),
                    $fullActionName,
                    $bace->getMessage()
                ),
                $fullActionName
            );
        }
    }

    /**
     * Sets the initAllActions flag: if true, all configured actions will be initialized; if false, only the requested actions will be initialized on request
     *
     * @param boolean $initAllActions init all actions flag value
     *
     * @return void
     */
    public function setInitAllActions($initAllActions)
    {
        $this->initAllActions = $initAllActions;
    }

    /**
     * Returns the initAllActions flag value
     *
     * @return boolean
     */
    public function getInitAllActions()
    {
        return $this->initAllActions;
    }

    /*********************************************
     *   G E T   A C T I O N S   M E T H O D S   *
     *********************************************/
    /**
     * Returns a proper ActionRequestInterface instance for the given Request Type
     *
     * @param string $requestType string representation of a request type (could be a class name or a string identifier)
     *
     * @return ActionRequestInterface
     *
     * @throws \Msl\RemoteHost\Exception\UnsupportedRequestTypeException
     */
    protected function getRequestInstance($requestType)
    {
        // First, we check if the request type is a valid namespace: if so, we instantiate the given class
        $className = null;
        if (class_exists($requestType)) {
            $className = $requestType;
        } elseif (class_exists(AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType)) {
            // If still not found, we add to the string the default namespace
            $className = AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType;
        } elseif (class_exists(AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType . AbstractActionRequest::REQUEST_CLASSNAME_SUFFIX)) {
            // If still not found, we add to the string a default class name suffix and the default namespace
            $className = AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType . AbstractActionRequest::REQUEST_CLASSNAME_SUFFIX;
        } else {
            throw new UnsupportedRequestTypeException(
                sprintf('[%s] Unsupported request type: \'%s\'', $this->getApiName(), $requestType),
                $requestType
            );
        }

        // If not found, we launch an exception
        $requestObj = new $className();
        if (!$requestObj instanceof ActionRequestInterface) {
            throw new UnsupportedRequestTypeException($this->getNotValidRequestExMsg($requestObj), $requestType);
        }

        // Return the ActionRequestInterface instance
        return $requestObj;
    }

    /**
     * Returns a proper ActionResponseInterface.php instance for the given Response Type
     *
     * @param string $responseType    string representation of a response type (could be a class name or a string identifier)
     * @param string $responseWrapper string representation of a response wrapper type (class name)
     *
     * @return ActionResponseInterface
     *
     * @throws \Msl\RemoteHost\Exception\UnsupportedResponseTypeException
     */
    protected function getResponseInstance($responseType, $responseWrapper = "")
    {
        // First, we check if the request type is a valid namespace: if so, we instantiate the given class
        $className = null;
        if (class_exists($responseType)) {
            $className = $responseType;
        } elseif (class_exists(AbstractActionRequest::RESPONSE_NAMESPACE . '\\' . $responseType)) {
            // If still not found, we add to the string the default namespace
            $className = AbstractActionRequest::RESPONSE_NAMESPACE . '\\' . $responseType;
        } elseif (class_exists(AbstractActionRequest::RESPONSE_NAMESPACE . '\\' . $responseType . AbstractActionRequest::RESPONSE_CLASSNAME_SUFFIX)) {
            // If still not found, we add to the string a default class name suffix and the default namespace
            $className = AbstractActionRequest::RESPONSE_NAMESPACE . '\\' . $responseType . AbstractActionRequest::RESPONSE_CLASSNAME_SUFFIX;
        } else {
            throw new UnsupportedResponseTypeException(
                sprintf('[%s] Unsupported response type: \'%s\'', $this->getApiName(), $responseType),
                $responseType
            );
        }

        // If not found, we launch an exception
        $responseObj = new $className();
        if (!$responseObj instanceof ActionResponseInterface) {
            throw new UnsupportedResponseTypeException($this->getNotValidRequestExMsg($responseObj), $responseType);
        }

        // Now checking if the given $responseWrapper parameter contains the namespace of a valid class name
        // that implements the interface \Msl\RemoteHost\Response\Wrapper\ResponseWrapperInterface
        $responseWrapperInterface = null;
        if (!is_null($responseWrapper) && !empty($responseWrapper)) {
            if (class_exists($responseWrapper)) {
                $responseWrapperInterface = new $responseWrapper;
                if ($responseWrapperInterface instanceof \Msl\RemoteHost\Response\Wrapper\ResponseWrapperInterface) {
                    $responseObj->setResponseWrapper($responseWrapperInterface);
                }
            }
        }

        // If no response wrapper, we set the default one
        if (is_null($responseWrapperInterface)) {
            $responseWrapperInterface = new DefaultResponseWrapper();
        }

        // We set a response wrapper
        if ($responseWrapperInterface instanceof \Msl\RemoteHost\Response\Wrapper\ResponseWrapperInterface) {
            $responseObj->setResponseWrapper($responseWrapperInterface);
        }

        // Return the ActionResponseInterface instance
        return $responseObj;
    }

    /**
     * Returns an array of required parameters from a well-configured ActionRequestInterface instance by action name
     *
     * @param string $actionName the action name
     *
     * @return array
     *
     * @throws \Msl\RemoteHost\Exception\NotConfiguredActionException
     */
    public function getActionRequestParametersByName($actionName)
    {
        // Return array by default
        $parameters = array();

        // Getting request object
        $request = $this->getActionRequestByName($actionName);
        if ($request instanceof AbstractActionRequest) {
            $parameters = $request->getParameters();
        }

        // Returning parameters array
        return $parameters;
    }

    /**
     * Returns a ActionRequestInterface instance by action name
     *
     * @param string $actionName the action name
     *
     * @return mixed
     *
     * @throws \Msl\RemoteHost\Exception\NotConfiguredActionException
     */
    public function getActionRequestByName($actionName)
    {
        // If action name level separator is in the string, we split
        $actionNameFirstPart = $actionNameSecondPart = "";
        if (strpos($actionName, self::API_NAME_LEVEL_SEPARATOR) === 0) {
            throw new BadApiConfigurationException(
                sprintf(
                    '[%s] Action name \'%s\' is not well formed. It should contain two parts separated by \'%s\'.',
                    $actionName,
                    self::API_NAME_LEVEL_SEPARATOR,
                    $this->getApiName()
                )
            );
        } else {
            $actionParts = explode(self::API_NAME_LEVEL_SEPARATOR, $actionName);
            if (isset($actionParts[0]) && isset($actionParts[1])) {
                $actionNameFirstPart  = $actionParts[0];
                $actionNameSecondPart = $actionParts[1];
            }
        }

        // Action is configured?
        if (!isset($this->actions[$actionNameFirstPart][$actionNameSecondPart])) {
            if ($this->getInitAllActions()) {
                throw new NotConfiguredActionException(
                    sprintf(
                        '[%s] No action configured with the following name: %s%s%s. Given action name was: %s',
                        $this->getApiName(),
                        $actionNameFirstPart,
                        self::API_NAME_LEVEL_SEPARATOR,
                        $actionNameSecondPart,
                        $actionName
                    ),
                    $actionName
                );
            } else {
                // We check if the requested action has a configuration
                if (!isset($this->actionsConfig[$actionNameFirstPart])
                    && !isset($this->actionsConfig[$actionNameFirstPart][$actionNameSecondPart])
                ) {
                    throw new NotConfiguredActionException(
                        sprintf(
                            '[%s] No action configuration found for the following action name: %s',
                            $this->getApiName(),
                            $actionName
                        ),
                        $actionName
                    );
                }

                // If configuration is found, then we create the action
                $this->setActionRequest(
                    $this->actionsConfig[$actionNameFirstPart][$actionNameSecondPart],
                    $actionNameFirstPart,
                    $actionNameSecondPart,
                    $this->actionsCommonConfig
                );
            }
        }

        // Returning action object
        return $this->actions[$actionNameFirstPart][$actionNameSecondPart];
    }

    /**
     * Returns an array of ActionRequestInterface instances for the given action group name
     *
     * @param string $actionGroupName the action group name
     *
     * @return mixed
     *
     * @throws \Msl\RemoteHost\Exception\NotConfiguredActionException
     */
    public function getActionRequestsGroupByName($actionGroupName)
    {
        // Action Group is configured?
        if (!isset($this->actions[$actionGroupName])) {
            throw new NotConfiguredActionException(
                sprintf(
                    '[%s] No action group configured with the following name: %s.',
                    $this->getApiName(),
                    $actionGroupName
                ),
                $actionGroupName
            );
        }

        // Returning action group
        return $this->actions[$actionGroupName];
    }

    /*****************************************************
     *   E X E C U T E   A C T I O N S   M E T H O D S   *
     *****************************************************/
    /**
     * Executes a remote api action by action name
     *
     * @param string $actionName         the action name to execute
     * @param array  $requestParameters  the request parameters (keys/values) to use in the action execution
     * @param string $content            the content to be set in the body of the request
     * @param array  $urlBuildParameters the url build adds on parameter array
     *
     * @return null|\Msl\RemoteHost\Response\Wrapper\ResponseWrapperInterface
     *
     * @throws \Msl\RemoteHost\Exception\NotConfiguredActionException
     * @throws \Msl\RemoteHost\Exception\BadConfiguredActionException
     * @throws \Msl\RemoteHost\Exception\UnsuccessApiActionException
     */
    public function execute($actionName, array $requestParameters = array(), $content = "", array $urlBuildParameters = array())
    {
        // Getting configured action request object
        $actionRequest = $this->getActionRequestByName($actionName);
        if (!$actionRequest instanceof AbstractActionRequest) {
            throw new BadConfiguredActionException($this->getNotValidRequestExMsg($actionRequest), $actionRequest);
        }

        // Setting all request parameters with the given values (request parameters array)
        try {
            $actionRequest->configure($requestParameters, $content, $urlBuildParameters);
        } catch (\Exception $ex) {
            throw new NotConfiguredActionException(
                sprintf(
                    '[%s] An error occured while configuring the action \'%s\': \'%s\'.',
                    $this->getApiName(),
                    $actionName,
                    $ex->getMessage()
                ),
                $actionRequest
            );
        }

        // We configure the Zend\Http\Client object and we send the request
        $actionRequest->setClientEncType($this->client);
        $response = $this->client->send($actionRequest);

        // We check the response: is it successful?
        if (!$response->isSuccess()) {
            throw new UnsuccessApiActionException(
                sprintf(
                    '[%s] The action \'%s\' returned the following error code \'%s\', message \'%s\' and content \'%s\'.',
                    $this->getApiName(),
                    $actionName,
                    $response->getStatusCode(),
                    $response->getReasonPhrase(),
                    $response->getContent()
                ),
                $actionRequest,
                $response
            );
        }

        // Now converting the response to an array
        $responseObj = $this->getResponseInstance(
            $actionRequest->getResponseType(),
            $actionRequest->getResponseWrapper()
        );

        // We check if there is a response object defined. If yes, we wrap the response into a response wrapper object;
        // if not, we return an array or a null value.
        if ($responseObj instanceof ActionResponseInterface) {
            $responseObj->setResponse($response);
            return $responseObj->getParsedResponse();
        }
        return null;
    }

    /*************************************
     *   G E N E R A L   M E T H O D S   *
     *************************************/
    /**
     * Returns a message for a not valid request object exception
     *
     * @param mixed $requestObj the request object to be checked
     *
     * @return string
     */
    protected function getNotValidRequestExMsg($requestObj)
    {
        // We check the type first
        if (is_object($requestObj)) {
            $type = get_class($requestObj);
        } else {
            $type = gettype($requestObj);
        }

        // We return the message
        return sprintf(
            '[%s] Unknown request object. Expected was \'%s\' but got \'%s\'.',
            $this->getApiName(),
            '\RemoteHost\Api\ActionRequestInterface',
            $type
        );
    }

    /**
     * Returns the name of the current api instance (for logs and exceptions)
     *
     * @return string
     */
    public function getApiName()
    {
        return $this->apiName;
    }
}