<?php
/**
 * Xml Action Response Implementation
 *
 * PHP version 5
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Response;

use Zend\Config\Reader\Xml;

/**
 * Xml Action Response Implementation
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class XmlActionResponse extends AbstractActionResponse
{
    /**
     * Converts the Response object body to an array
     *
     * @return array
     */
    public function bodyToArray()
    {
        // Getting response content
        $xmlContent = $this->response->getContent();

        // Wrapping it into a Xml reader object and convert it to array
        $xmlReader = new Xml();
        return $xmlReader->fromString($xmlContent);
    }
}