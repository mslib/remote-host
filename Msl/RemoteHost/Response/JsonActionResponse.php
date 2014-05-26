<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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