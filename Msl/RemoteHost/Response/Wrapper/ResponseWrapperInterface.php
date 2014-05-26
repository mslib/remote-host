<?php
/**
 * Response Wrapper Interface
 *
 * PHP version 5
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response\Wrapper
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
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