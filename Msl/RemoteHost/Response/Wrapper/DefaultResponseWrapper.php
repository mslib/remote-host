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