<?php
/**
 * Json Action Response Implementation
 *
 * PHP version 5
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Response;

use Zend\Config\Reader\Json;

/**
 * Json Action Response Implementation
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class JsonActionResponse extends AbstractActionResponse
{
    /**
     * Converts the Response object body to an array
     *
     * @return array
     */
    public function bodyToArray()
    {
        // Getting response content
        $jsonContent = $this->response->getContent();

        // Wrapping it into a Json reader object and convert it to array
        $jsonReader = new Json();
        return $jsonReader->fromString($jsonContent);
    }
}