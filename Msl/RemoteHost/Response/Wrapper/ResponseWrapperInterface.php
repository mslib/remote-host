<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\RemoteHost\Response\Wrapper;

use Msl\RemoteHost\Response\ActionResponseInterface;

/**
 * Response Wrapper Interface
 *
 * @category  Wrapper
 * @package   Msl\RemoteHost\Response\Wrapper
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
interface ResponseWrapperInterface
{
    /**
     * Returns response raw data
     *
     * @return mixed
     */
    public function getRawData();

    /**
     * Initializes the object fields with the given raw data
     *
     * @param array                                             $rawData        array containing the response raw data
     * @param \Msl\RemoteHost\Response\ActionResponseInterface  $actionResponse the action response object from which to extract additional information
     *
     * @return mixed
     */
    public function init(array $rawData, ActionResponseInterface $actionResponse);

    /**
     * Returns the body of the Response
     *
     * @return mixed
     */
    public function getBody();

    /**
     * Returns an array containing the data to be used in the hydration process of a given object
     *
     * @return array
     */
    public function getHydrationData();

    /**
     * Returns true if the Http status code is 200 or 201 or 204; false otherwise
     *
     * @return bool
     */
    public function isHttpStatusSuccessful();

    /**
     * Returns true if the http status code is equal to 404; false otherwise
     *
     * @return bool
     */
    public function isNotFoundHttpStatus();

    /**
     * @return \Zend\Http\Response
     */
    public function getServerRawResponse();

    /**
     * Returns the request name (name or uri) for this wrapped response object
     *
     * @return string
     */
    public function getRequestName();

    /**
     * Inits the status field from the response
     *
     * @return mixed
     */
    public function initStatusFromResponse();
}