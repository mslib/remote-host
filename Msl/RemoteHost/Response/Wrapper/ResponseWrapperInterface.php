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
     * @param array $rawData array containing the response raw data
     *
     * @return mixed
     */
    public function init(array $rawData);

    /**
     * Returns the body of the Response
     *
     * @return mixed
     */
    public function getBody();
}