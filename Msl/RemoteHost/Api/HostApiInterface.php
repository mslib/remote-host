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
use Msl\RemoteHost\Api\ActionRequest;

/**
 * Host Api Interface
 *
 * @category  Api
 * @package   Msl\RemoteHost\Api
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
interface HostApiInterface
{
    /**
     * Sets the configuration
     *
     * @param array $configuration The configuration array
     *
     * @return mixed
     */
    public function setConfiguration(array $configuration);

    /**
     * Configures the client configs
     *
     * @param array $configs the http client configuration array
     *
     * @return mixed
     */
    public function configureClientConfig(array $configs);

    /**
     * Executes a remote api action by action name
     *
     * @param string $actionName         the action name to execute
     * @param array  $requestParameters  the request parameters (keys/values) to use in the action execution
     * @param string $content            the content to be set in the body of the request
     * @param array  $urlBuildParameters the url build adds on parameter array
     * @param array  $headersValue       the header value array to override default header values
     *
     * @return mixed
     */
    public function execute($actionName, array $requestParameters = array(), $content = "", array $urlBuildParameters = array(), array $headersValue = array());

    /**
     * Sets all general parameters for all api calls
     *
     * @param array $apiCallParameters the api call parameters array
     *
     * @return mixed
     */
    public function setGeneralApiCallParameters(array $apiCallParameters);
}