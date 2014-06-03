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
use Zend\Http\Response;

/**
 * Default Response Wrapper: simply wrap the raw data into an object
 *
 * @category  Response\Wrapper
 * @package   Msl\RemoteHost\Response\Wrapper
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class DefaultResponseWrapper extends AbstractResponseWrapper
{
    /**
     * Initializes the object fields with the given raw data
     *
     * @param array                                             $rawData        array containing the response raw data
     * @param ActionResponseInterface  $actionResponse the action response object from which to extract additional information
     *
     * @return mixed
     */
    public function init(array $rawData, ActionResponseInterface $actionResponse)
    {
        // Setting raw data field
        $this->rawData = $rawData;

        // Setting wrapper status and message fields
        $response = $actionResponse->getResponse();
        $this->returnCode    = $response->getStatusCode();
        $this->returnMessage = $response->getReasonPhrase();
        if ($this->returnCode === Response::STATUS_CODE_200
            || $this->returnCode === Response::STATUS_CODE_201
                || $this->returnCode === Response::STATUS_CODE_202
                    || $this->returnCode === Response::STATUS_CODE_204
        ) {
            $this->status = true;
        } else {
            $this->status = false;
        }
    }

    /**
     * Return the body of the Response.
     *
     * @return array
     */
    public function getBody()
    {
        return $this->rawData;
    }
}