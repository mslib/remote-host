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
     * @param array  $requestValues      the request parameters
     * @param string $content            the body content
     * @param array  $urlBuildParameters the url build adds on parameter array
     * @param array  $headersValue       the header value array to override default header values
     *
     * @return mixed|void
     *
     * @throws \Msl\RemoteHost\Exception\BadConfiguredActionException
     *
     */
    public function configure(array $requestValues, $content = "", array $urlBuildParameters = array(), array $headersValue = array())
    {
        // Set request parameters in parent entity
        parent::configure($requestValues, $content, $urlBuildParameters, $headersValue);

        // Set the request body content
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