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

/**
 * Json Action Request Object: extension of Zend\Http\Request
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class JsonActionRequest extends AbstractActionRequest
{
    /**
     * Configures an action request with the given request values and content
     *
     * @param array  $requestValues      the request parameters
     * @param string $content            the body content
     * @param array  $urlBuildParameters the url build adds on parameter array
     * @param array  $headersValue       the header value array to override default header values
     *
     * @return mixed|void
     *
     * @throws \Msl\RemoteHost\Exception\BadConfiguredActionException
     */
    public function configure(array $requestValues, $content = "", array $urlBuildParameters = array(), array $headersValue = array())
    {
        // Calling parent configuration
        parent::configure($requestValues, $content, $urlBuildParameters, $headersValue);

        // Getting parameters with values
        $parameters = $this->getParametersWithValue($requestValues);
        $jsonParams = json_encode($parameters);

        // Set the request body content
        $this->getHeaders()->addHeaderLine('content-type', 'application/json');
        $this->setContent($jsonParams);
    }

    /**
     * Sets a proper EncType on the given \Zend\Http\Client object (for Json Request, no encryption type is set)
     *
     * @param \Zend\Http\Client $client the Zend http client object
     *
     * @return mixed|\Zend\Http\Client
     */
    public function setClientEncType(\Zend\Http\Client $client)
    {
        // Not required for Json requests
        return $client;
    }
}