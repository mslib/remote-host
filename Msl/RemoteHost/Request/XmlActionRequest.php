<?php
/**
 * Xml Action Request Object: extension of UrlEncodedActionRequest
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
 * Xml Action Request Object: extension of UrlEncodedActionRequest
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class XmlActionRequest extends UrlEncodedActionRequest
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
        $this->getHeaders()->addHeaderLine('content-type', 'text/xml');
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