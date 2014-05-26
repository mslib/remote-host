<?php
/**
 * UrlEncoded Action Request Object: extension of Zend\Http\Request
 *
 * PHP version 5
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Request;

use Zend\Http\Request;

/**
 * UrlEncoded Action Request Object: extension of Zend\Http\Request
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class UrlEncodedActionRequest extends AbstractActionRequest
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
     */
    public function configure(array $requestValues, $content = "", $trimRequestName = true)
    {
        // Calling parent configuration
        parent::configure($requestValues, $content, $trimRequestName);

        // Getting parameters with values
        $parameters = $this->getParametersWithValue($requestValues);

        // We set the parameters according to the method
        if ($this->isGet()) {
            $this->getQuery()->fromArray($parameters);
        } else if ($this->isPost()) {
            $this->getPost()->fromArray($parameters);
        }
    }

    /**
     * Sets a proper EncType on the given \Zend\Http\Client object (for UrlEncoded Request, used value is Client::ENC_URLENCODED)
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