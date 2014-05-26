<?php
/**
 * Post Text Action Request Object: extension of AbstractActionRequest
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
 * Post Text Action Request Object: extension of AbstractActionRequest
 *
 * @category  Request
 * @package   Msl\RemoteHost\Request
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class PostTextActionRequest extends AbstractActionRequest
{
    /**
     * Configures an action request with the given request values and content
     *
     * @param array  $requestValues   the request parameters
     * @param string $content         the body content
     * @param bool   $trimRequestName remove or not final '/' from action name or not
     *
     * @return mixed|void
     *
     * @throws \Msl\RemoteHost\Exception\BadConfiguredActionException
     */
    public function configure(array $requestValues, $content = "", $trimRequestName = true)
    {
        // Set request parameters in parent entity
        parent::configure($requestValues, $content, $trimRequestName);

        // We set the parameters according to the method
        if ($this->isGet()) {
            $this->getQuery()->fromString($content);
        } else if ($this->isPost()) {
            $this->getPost()->fromString($content);
        }

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