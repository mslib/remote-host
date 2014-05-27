**REMOTEHOST LIBRARY**
======================

This library provides an easy way to send requests to Web APIs. 

It is based on ZendFramework2. The following ZF2 modules are currently used 
(take a look at the composer.json for a full overview about all dependencies):

```js
{
    "require": {
        "zendframework/zend-http": "2.*",
        "zendframework/zend-config": "2.*",
        "zendframework/zend-json": "2.*",
        "zendframework/zend-uri": "2.*"
    }
}
```
The library was ONLY tested for requests sent with the Curl library. For stability purposes, please make sure 
that all request objects configured with this library are an extension of 'Zend\Http\Client\Adapter\Curl' 
class (for more details about this class, please refer to the ZF2 documentation). 

For more information about how to define different adapter for all requests, please refer to the paragraph 
'Step2: Configure your api calls' of this documentation.


**INSTALLATION**
----------------

Installation is a quick 3 step process:

1. Download RemoteHost using composer
2. Configure your api calls 
3. Generate your Api implementation

### Step 1: Download RemoteHost using composer

Add RemoteHost in your composer.json:

```js
{
    "require": {
        "mslib/remote-host": "dev-master"
    }
}
```

Now tell composer to download the library by running the command:

``` bash
$ php composer.phar update mslib/remote-host
```

Composer will install the library to your project's `vendor/mslib/remote-host` directory.

### Step 2: Configure your api calls

Now that the RemoteHost library is available in your project (through composer, for example), 
you need to configure all required API actions that your application needs.

In order to do that, you have to create a PHP file containing an array with a given set of configuration 
keys and values. You will find here below the structure of such a file.

``` php
<?php

return array(
    'parameters' => array(
        'host'      => '', // (REQUIRED) The host api
        'port'      => '', // (optional) The port
        'user'      => '', // (optional) The user
        'password'  => '', // (optional) The password
    ),
    'actions_parameters' => array(
        // Add here all the parameters which are common to all actions. You still can override them by adding a different value in the parameters array of each action
        'key1' => 'value1',
        ...
    ),
    // For all possible values, please look at Zend\Http\Client->$config
    'config' => array(
        'adapter'       => 'Zend\Http\Client\Adapter\Curl', // Please, leave this value for stability reason
        ...
    ),
    'actions' => array(
        'action-group' => array( // (REQUIRED) First Level Action Name 
            'action-name' => array( // (REQUIRED) Second Level Action Name 
                'name'              => '{API action name or url part to be concatenated to the base url (e.g. action1)}', // (REQUIRED) the name of the action
                'request'           => array(
                    'adds_on'       => array( // Expressions to be added to the base url // e.g. 'name/protocol/entity/{id}/get
                        // here you can put as many adds on array as many you desire; they will be added to the base url in the given order
                        array(
                            'type'    => 'plain|replace', // the type of add on: 'plain' will simply concatenate the string given in the content field; 'replace' will first replace all parameter name within brackets '{{xxx}} and then concatenate it to the url
                            'content' => 'the content' // e.g. plain -> 'text-to-be-concatenated'; 'replace' => 'resource/{{id}}/get'
                        ),
                        array(
                            ...
                            ...
                        ),
                        ...
                        ...
                    ),
                    'type'                     => 'UrlEncoded|PostText|Xml|{Custom Class Name with full namespace that extends class Msl\RemoteHost\Request\AbstractActionRequest}', // 'UrlEncoded', 'PostText', 'Xml' are the request implementations available with the library
                    'method'                   => 'GET', // (optional) POST is the default value
                    'parameters'               => array( // (optional) array containing all request parameters (this array will be merged with the action_parameters defined above)
                        'key1' => 'value1', // put here a default value for each parameter; note that the default values will be overriden with the values passed in the execute method
                        ...
                    ),
                    'host'          => '', //(you can override here the general host; if left empty or not specified, the default value will be used)
                    'port'          => '', //(you can override here the general port; if left empty or not specified, the default value will be used)
                ),
                'response' => array(
                    'type'      => 'Json|PlainText|Xml|{Custom Class Name with full namespace that extends class Msl\RemoteHost\Response\AbstractActionResponse}', // (REQUIRED) Json, PlainText, Xml are the response implementations available with the library
                    'wrapper'   => '{Custom Class Name with full }', // (optional) if not specified, the default wrapper \Msl\RemoteHost\Response\Wrapper\DefaultResponseWrapper will be used
                ),
            ),
        ),

    ),
);
```

For example, let's suppose that you want to send requests to an API whose base url is *'http://www.example.com/api'*. 
Let's also suppose that the name of an available action is *'scripts'*, which returns the list of all the scripts available 
on the server. The response format is either Json or Xml, according to the value of the request parameter 'format'. The request 
method is POST and the request parameters should be sent as part of the request itself (i.e. no xml). 

A call to such a function would need the following configuration:

``` php
<?php

return array(
    'parameters' => array(
        'host'      => 'http://www.example.com/api/', 
    ),
    'actions_parameters' => array(
    ),
    'config' => array(
        'maxredirects'  => 2,
        'timeout'       => 30,
        'adapter'       => 'Zend\Http\Client\Adapter\Curl',
    ),
    'actions' => array(
        'example-api' => array( 
            'script-list' => array( 
                'name'              => 'scripts',
                'adds-on'           => array(
                    array(
                        'type'    => 'plain',
                        'content' => 'scripts'
                    )
                ),
                'request'           => array(
                    'type'                     => 'UrlEncoded', 
                    'parameters'               => array( 
                        'format'      => 'json', 
                    ),
                ),
                'response' => array(
                    'type'      => 'Json', 
                ),
            ),
        ),
    ),
);
```

The above configuration is equivalent to sending a Curl request with the following parameters:

* **Method**: POST
* **Request Url**: http://www.example.com/api/scripts?format=json

The response expected is of type Json and it will be wrapped in the library default wrapper class 
*'Msl\RemoteHost\Response\Wrapper\DefaultResponseWrapper'* (For more details about Response Wrappers, 
please refer to the dedicated section '*BUILT-IN RESPONSE WRAPPERS*' of this documentation).

### Step3: Generate your Api implementation

Now that you have configured all required API actions, you need to create, in your project, a class that extends 
the base abstract class *'Msl\RemoteHost\Api\AbstractHostApi'*, defined in the library RemoteHost.

The class *'Msl\RemoteHost\Api\AbstractHostApi'* has an abstract method *'getDefaultConfig()'* that 
should therefore be implemented in your child class. This method should return the content of the array 
defined at step 2.

The default class constructor requires two parameters: 

* ***$apiName***: the name of the api to be used in logs, exceptions, etc. (e.g. 'MY_API'); the default value is defined in the class constant Msl\RemoteHost\Api\AbstractHostApi::API_NAME; to override this value, you either pass a valid string as the first parameter of the class constructor, or you redefine the constant API_NAME in your child class;
* ***$config***: the array containing the configuration defined at step 2;


**BASIC USE**
-------------

### Calling a configured action

Once that you have configured all the required actions and that you have created an API class that extends 
the base abstract class *'Msl\RemoteHost\Api\AbstractHostApi'*, you are ready to launch requests.

To do that, you have to use the method *execute()* of your custom API class, which requires the following parameters:

* ***$actionName***: the action name to execute (required);
* ***$requestParameters***: the request parameters (keys/values) to use in the action execution (optional -> default value is an empty array);
* ***$content***: the content to be set in the body of the request (optional -> default value is an empty string);
* ***$trimRequestName***: remove or not final '/' from action name (optional -> default value is true);

The first parameter ***$actionName*** corresponds to a string obtained by the concatenation of the two configuration levels 
(separated by ***'.'***) of the desired action, as configured in the subelement *actions* of the general configuration array.

For example, let's consider the following configuration:

``` php
<?php

return array(
    'parameters' => array(
        'host'      => 'http://www.example.com/api/', 
    ),
    'actions_parameters' => array(
    ),
    'config' => array(
        'maxredirects'  => 2,
        'timeout'       => 30,
        'adapter'       => 'Zend\Http\Client\Adapter\Curl',
    ),
    'actions' => array(
        'example-api' => array( 
            'script-list' => array( 
                'name'              => 'scripts', 
                'request'           => array(
                    'type'                     => 'UrlEncoded', 
                    'parameters'               => array( 
                        'format'      => 'json', 
                    ),
                ),
                'response' => array(
                    'type'      => 'Json', 
                ),
            ),
        ),

    ),
);
```
In order to send a request for the action configured by the following sub-array 

``` php
<?php

return array(
    ...
    ...
    'actions' => array(
        'example-api' => array( 
            'script-list' => array( 
                ...
                ...
            ),
        ),

    ),
    ...
    ...    
);
```
you will have to pass the following string as the first parameter of the *execute()* method:

> ***example-api.script-list***

So, to call the action *example-api.script-list* with the default parameters (as configured in the general configuration array), you will have to use the following line in our code:

``` php
<?php      
    ...
    ...
    // ApiImplementation is a child class of Msl\RemoteHost\Api\AbstractHostApi
    $api = new ApiImplementation();
    
    // Calling action script-list
    $result = $api->execute('example-api.script-list');
    ...
    ...    
```

### Calling a configured action with some specific parameters

If you need to call a given configured action with some specific parameters, then you have to put these values in 
associative array and pass it as the second parameter of the function *execute()*, as shown here below:

``` php
<?php        
    ...
    ...
    // ApiImplementation is a child class of Msl\RemoteHost\Api\AbstractHostApi
    $api = new ApiImplementation();
        
    // Creating parameters array: here we can override all the default parameters defined in the configuration array
    $parameters = array('format'=>'xml');
    
    // Calling action script-list
    $result = $api->execute('example-api.script-list', $parameters);
```

### Calling a configured action with some specific body content

If you need to call a given configured action with some specific body content, then you have to pass such content 
as the third parameter of the function *execute()*, as shown here below:

``` php
<?php
    ...
    ...
    // ApiImplementation is a child class of Msl\RemoteHost\Api\AbstractHostApi
    $api = new ApiImplementation();
        
    // Creating parameters array: here we can override all the default parameters defined in the configuration array
    $parameters = array();
    
    // Creating request content: this could be an XML content for example
    $content = 'text content';
    
    // Calling action script-list
    $result = $api->execute('example-api.script-list', $parameters, $content);
```

### The *GoogleApi* implementation example

For a full example of how to use this library, please refer to the repository 'mslib/directions-demo', which implements an API connector layer for Google Maps API.

Here follows a quick description of the example repository just mentioned.

Let's suppose that you want to implement a class that wraps all Google API calls. To do that, you need to create a class that extends the base abstract class 'Msl\RemoteHost\Api\AbstractHostApi' and define a configuration for it.

Let's start with the implementation of the Google API function 'directions' as documented at the following url:

> [*https://developers.google.com/maps/documentation/directions/?hl=en*](https://developers.google.com/maps/documentation/directions/?hl=en)

#### Configuration

Let's suppose that you want to implement a call to the *'directions'* Google API with the following configuration:

* ***Method***: GET
* ***Request Url***: http://maps.googleapis.com/maps/api/directions/json
* ***Response Type***: JSON
* ***Request parameter***: origin, destination, sensor

A request url example is the following: [*http://maps.googleapis.com/maps/api/directions/json?origin=Toronto&destination=Montreal&sensor=false*]

The configuration for such a call would be as follows:

``` php
<?php

return array(
    'parameters' => array(
        'host'      => 'http://maps.googleapis.com/maps/api/', // The host api
    ),
    'actions_parameters' => array(
    ),
    // For all possible values, please look at Zend\Http\Client->$config
    'config' => array(
        'maxredirects'  => 2,
        'timeout'       => 30,
        'adapter'       => 'Zend\Http\Client\Adapter\Curl',
    ),
    'actions' => array(
        'google-json' => array(
            'driving-directions' => array(
                'name'              => 'directions/json',
                'request'           => array(
                    'adds_on'       => array(
                        array ( // (e.g. http://maps.googleapis.com/maps/api/directions/json)
                            'type'   => 'plain',
                            'content => 'directions/json'
                        )
                    ),
                    'type'                     => 'UrlEncoded',
                    'method'                   => 'GET', 
                    'parameters'               => array( 
                        'origin'      => '', // default value for each parameter; default values will be overriden with the values passed in the execute method
                        'destination' => '',
                        'sensor'      => '',
                    ),
                ),
                'response' => array(
                    'type'      => 'Json', 
                ),
            ),
        ),
    ),
);
```

#### API Class

Now that you have prepared the configuration for the *'destinations'* API call, you need to implement our API Class, that will be in charge of connecting to remote API and send a request to it.

To do that, you should implement a class called *'GoogleApi'* that extends the base *'Msl\RemoteHost\Api\AbstractHostApi'* abstract class as follows:

``` php
<?php

class GoogleApi extends AbstractHostApi
{
    /**
     * String containing the name of this api. This value will be used mainly for log purposes.
     *
     * @var string
     */
    const API_NAME = 'GOOGLE_API';

    /**
     * Returns the default config array
     *
     * @return mixed
     */
    public function getDefaultConfig()
    {
        return include __DIR__ . '/resources/config/googlehost.config.php';
    }    
}
```

Note that:

* you have implemented the parent abstract method 'getDefaultConfig' that should return the configuration array defined at the previous step. Let's suppose that such array is stored in a file whose path is '/resources/config/googlehost.config.php'.
* you have redefined the constant 'API_NAME' so that it carries the value 'GOOGLE_API'; this could be useful for logging purposes;


#### API Methods

The last step is to define a method in the *'GoogleApi'* class, so that you can wrap the call to the configured action *'google-json.driving-directions'* in a method. To do that, you should add a method *'getRoutes()'* as here below:

``` php 
<?php

namespace Connector\Api\Google;

use Msl\RemoteHost\Api\AbstractHostApi;

class GoogleApi extends AbstractHostApi
{
    /**
     * String containing the name of this api. This value will be used mainly for log purposes.
     *
     * @var string
     */
    const API_NAME = 'GOOGLE_API';

    /**
     * Returns the default config array
     *
     * @return mixed
     */
    public function getDefaultConfig()
    {
        return include __DIR__ . '/resources/config/googlehost.config.php';
    }

    /**
     * A Directions API request
     *
     * @param string $origin      The address or textual latitude/longitude value from which you wish to calculate directions
     * @param string $destination The address or textual latitude/longitude value from which you wish to calculate directions
     * @param string $sensor      whether or not the directions request comes from a device with a location sensor
     *
     * @return \Msl\RemoteHost\Response\AbstractResponseWrapper
     */
    public function getRoutes($origin, $destination, $sensor)
    {

        /** @var /ResponseWrapper/JsonGoogleResponseWrapper $response */
        $response = null;
        try {
            $response = $this->execute(
                'google-json.driving-directions',
                array(
                    'origin'      => $origin,
                    'destination' => $destination,
                    'sensor'      => $sensor,
                )
            );
        } catch (\Exception $e) {
            echo sprintf('[%s] Google Host call failed! Error message is: \'%s\'', $this->getApiName(), $e->getMessage());
        }

        return $response;
    }
}
```
As you can see, this method has three parameters: origin, destination and sensor. These parameters corresponds to the required Google API call parameter origin, destination and sensor as explained in Google API documentation for the action 'directions'.

**BUILT-IN REQUESTS**
---------------------

All requests sent by an implementation of *'Msl\RemoteHost\Api\AbstractHostApi'* will be wrapped in a request object, which extends the abstract class 'Msl\RemoteHost\Request\AbstractActionRequest'.

The class *'Msl\RemoteHost\Request\AbstractActionRequest'* is an extension of the class *'Zend\Http\Request'*.

### The AbstractActionRequest class

The *AbstractActionRequest* implements the interface *'Msl\RemoteHost\Request\ActionRequestInterface'*, which defines the following methods to be implemented in the child class:

* ***init()***: initializes the request object (sets request type, response and response wrapper types, request method, base url, port, etc.);
* ***configure()***: configures an action request (sets request parameter, content, headers, etc.);
* ***setClientEncType()***: sets the required encoding method;

The *AbstractActionRequest* class has a default implementation of the method *init()*, but not for the methods *configure()* and *setClientEncType()*.

### The built-in request objects

The built-in request objects are: 

* ***UrlEncodedActionRequest***: used to send a classic GET or POST requests;
* ***XmlActionRequest***: used to send a request with an Xml content type;
* ***PostTextActionRequest***: used to send a request with a Simple Text content type;

#### The UrlEncoded request

The UrlEncoded request object can be used to send a request with parameters encoded in the request. 

It supports the POST and GET http methods.

In order to use it, you need to use the label *'UrlEncoded'* for the configuration key *'request.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'request'           => array(
                    'type' => 'UrlEncoded', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

#### The Xml request

The Xml request object can be used to send a request with an Xml content. 

In order to use it, you need to use the label *'Xml'* for the configuration key *'request.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'request'           => array(
                    'type' => 'Xml', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

#### The PostText request

The PostText request object can be used to send a request with a simple text content. 

In order to use it, you need to use the label *'PostText'* for the configuration key *'request.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'request'           => array(
                    'type' => 'PostText', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

#### Load your Custom Request objects

You can also define your own request objects by extending the base abstract class *'Msl\RemoteHost\Request\AbstractActionRequest'* and use it in the configuration file.

Here follow a class implementation and a configuration file for a custom request object.

``` php
<?php

namespace Msl\Example\Request;

class GoogleRequestActionRequest extends AbstractActionRequest
{
    /**
     * Configures an action request with the given request values and content
     *
     * @param array     $requestValues      the request parameters
     * @param string    $content            the body content
     * @param bool      $trimRequestName    remove or not final '/' from action name or not
     *
     * @return mixed|void
     *
     * @throws \Msl\RemoteHost\Exception\BadConfiguredActionException
     *
     */
    public function configure(array $requestValues, $content = "", $trimRequestName = true)
    {
        // Set request parameters in parent entity
        parent::configure($requestValues, $content, $trimRequestName);

        // Set the result to the request body
        $this->setContent($content);
    }

    /**
     * Sets a proper EncType on the given \Zend\Http\Client object (for Xml Request, used value is Client::ENC_URLENCODED)
     *
     * @param \Zend\Http\Client $client the Zend http client object
     *
     * @return mixed|\Zend\Http\Client
     */
    public function setClientEncType(\Zend\Http\Client $client)
    {
        // Setting EncType to UrlEncoded
        $client->setEncType(\Zend\Http\Client::ENC_URLENCODED);

        return $client;
    }
}
```

You can then use your custom request object by specifying the full class namespace in the configuration key *'request.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'request'           => array(
                    'type' => 'Msl\Example\Request\GoogleRequestActionRequest', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

**BUILT-IN RESPONSES**
----------------------

All response received by an implementation of 'Msl\RemoteHost\Api\AbstractHostApi' will be wrapped in a response object, which extends the abstract class 'Msl\RemoteHost\Response\AbstractActionResponse'.

### The AbstractActionResponse class

The *AbstractActionResponse* implements the interface *'Msl\RemoteHost\Response\ActionResponseInterface'*, which defines the following methods to be implemented in the child class:

* ***setResponse()***: sets the \Zend\Http\Response object in a given response object;
* ***setResponseWrapper()***: sets a ResponseWrapperInterface implementation in a given response object (see section below for an explaination of the response wrapper objects);
* ***bodyToArray()***: converts the body of the response to an array according to the response type (json to array, xml to array, text to array, etc.);
* ***getParsedResponse()***: returns a ResponseWrapperInterface instance;

The *AbstractActionResponse* class has a default implementation of the method *setResponse()*, *setResponseWrapper()* and *getParsedResponse()*, but not for the method *bodyToArray()*.

### The built-in response objects

The built-in response objects are: 

* ***JsonActionResponse***: used for all Json responses;
* ***XmlActionResponse***: used for all Xml responses;
* ***PlainTextActionResponse***: used for all Simple Text responses;

### The Json response

The Json response object can be used when we receive a Json response for a given request. 

In order to use it, you need to use the label *'Json'* for the configuration key *'response.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'response'           => array(
                    'type' => 'Json', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

### The Xml response

The Xml response object can be used when we receive a Xml response for a given request. 

In order to use it, you need to use the label *'Xml'* for the configuration key *'response.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'response'           => array(
                    'type' => 'Xml', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

### The PlainText response

The PlainText response object can be used when we receive a plain text response for a given request. 

In order to use it, you need to use the label *'PlainText'* for the configuration key *'response.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'response'           => array(
                    'type' => 'PlainText', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

### Load your Custom Response objects

You can also define your own response objects by extending the base abstract class *'Msl\RemoteHost\Response\AbstractActionResponse'* and use it in the configuration file.

Here follow a class implementation and a configuration file for a custom response object.

``` php
<?php

namespace Msl\Example\Request;

class GoogleResponseActionResponse extends AbstractActionResponse
{
    /**
     * Converts the Response object body to an array
     *
     * @return array
     */
    public function bodyToArray()
    {
        // Getting response content
        $responseContent = $this->response->getContent();

        // We parse the content in a custom way and we convert it to an array...
        $customContentAsArray = array();
        
        ...........
        ...........
        ...........
        
        return $customContentAsArray;
    }
}
```
You can then use your custom response object by specifying the full class namespace in the configuration key *'response.type'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'response'           => array(
                    'type' => 'Msl\Example\Response\GoogleResponseActionResponse', 
                    ...
                ),
                ...
            ),
        ),
    ),
);
```

**BUILT-IN RESPONSE WRAPPERS**
------------------------------

All Response objects (point above) are wrapped into a *ResponseWrapper* object, which 'extracts' some additional information from the Response object.

This additional information includes:

- The Response Status;
- The Response Return Code;
- The Response Return Message associated to the found Return Code;

All Response Wrapper objects should extend the base abstract class *'Msl\RemoteHost\Response\Wrapper\AbstractResponseWrapper'*, which implements the interface *'Msl\RemoteHost\Response\Wrapper\ResponseWrapperInterface'*.

### The Default response wrapper

If you do not define a Response Wrapper in the configuration key *'response.wrapper'* , the default wrapper \Msl\RemoteHost\Response\Wrapper\DefaultResponseWrapper will be used.

### Load your Custom Response Wrapper objects

You can also define your own Response Wrapper objects by extending the base abstract class *'Msl\RemoteHost\Response\Wrapper\AbstractResponseWrapper'* and use it in the configuration file.

Here follow a class implementation and a configuration file for a custom response wrapper object.

``` php
<?php

use Msl\RemoteHost\Response\Wrapper\AbstractResponseWrapper;

/**
 * Json Google Response Wrapper for Google Actions
 */
class JsonGoogleResponseWrapper extends AbstractResponseWrapper
{
    /**
     * Defaults status strings
     */
    const STATUS_NOT_FOUND = "NOT_FOUND";
    const STATUS_OK        = "OK";

    /**
     * Initializes the object fields with the given raw data.
     *
     * @param array $rawData an array containing the raw response
     *
     * @return mixed
     */
    public function init(array $rawData)
    {
        // Setting raw data field
        $this->rawData = $rawData;

        // Setting status, returnCode and returnMessage fields to the ResponseWrapper entity
        if (is_array($rawData)) {
            // Setting  return code and return message
            if (isset($rawData['status'])) {
                if ($rawData['status'] === self::STATUS_OK) {
                    $this->status        = true;
                    $this->returnCode    = self::STATUS_OK;
                    $this->returnMessage = self::STATUS_OK;
                } else {
                    $this->status        = false;
                    $this->returnCode    = self::STATUS_NOT_FOUND;
                    $this->returnMessage = self::STATUS_NOT_FOUND;
                }
            }
        }
    }

    /**
     * Returns the found routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->getBody();
    }

    /**
     * Return the body of the Response.
     *
     * @return array
     */
    public function getBody()
    {
        if (isset($this->rawData['routes'])) {
            return $this->rawData['routes'];
        }
        return array();
    }
}
```
You can then use your custom response wrapper object by specifying the full class namespace in the configuration key *'response.wrapper'* of a given configured action as follows:

``` php
<?php

return array(
    'parameters' => array(
        ...
    ),
    ...
    'actions' => array(
        'example-api' => array( 
            'action-1' => array( 
                'name'              => 'scripts', 
                'response'           => array(
                    'type'    => 'Json', 
                    'wrapper' => 'Connector\Api\Google\ResponseWrapper\JsonGoogleResponseWrapper', 
                ),
                ...
            ),
        ),
    ),
);
```