<?php
/**
 * Plain Text Action Response Implementation
 *
 * PHP version 5
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */

namespace Msl\RemoteHost\Response;

/**
 * Plain Text Action Response Implementation
 *
 * @category  Response
 * @package   Msl\RemoteHost\Response
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class PlainTextActionResponse extends AbstractActionResponse
{
    /**
     * Converts the Response object body to an array
     *
     * @return array
     */
    public function bodyToArray()
    {
        // Getting response content
        $content = $this->response->getContent();

        // Wrapping it into an array and return it
        return array($content);
    }
}