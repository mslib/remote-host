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
     * Inits the status field from the response
     *
     * @return mixed
     */
    public function initStatusFromResponse()
    {
        // Setting wrapper status
        if ($this->serverRawResponse->isSuccess()) {
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