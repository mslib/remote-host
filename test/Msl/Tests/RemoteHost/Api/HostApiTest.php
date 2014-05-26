<?php
/**
 * This file is part of the RemoteHost package.
 *
 * (c) Marco Spallanzani <mslib.code@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Msl\Tests\RemoteHost\Api;

use Msl\Tests\RemoteHost\RemoteHostTestCase;
use Msl\RemoteHost\Api\AbstractHostApi;

/**
 * Host Api Test: Test for class Msl\RemoteHost\Api\AbstractHostApi
 *
 * @category  Api
 * @package   Msl\Tests\RemoteHost\Api
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class HostApiTest extends RemoteHostTestCase
{
    /*********************************
     *  D A T A   P R O V I D E R S  *
     *********************************/
    /**
     * DataProvider for 'getCategoryById' tests.
     * @return array
     */
    public static function providerMockApi()
    {
        return array(
            array(
                'MOCK_API',
                array(
                    'parameters' => array(
                        'host'      => 'http://www.example.com/api/',
                    ),
                    'config' => array(
                        'maxredirects'  => 2,
                        'timeout'       => 30,
                        'adapter'       => 'Zend\Http\Client\Adapter\Curl',
                    ),
                    'actions' => array(
                        'unit-test-1' => array(
                            'action-1' => array(
                                'name'              => 'action/1',
                                'request'           => array(
                                    'name_in_uri'   => true,
                                    'type'              => 'UrlEncoded',
                                    'method'            => 'GET',
                                    'parameters'        => array(
                                        'param1' => '',
                                        'param2' => '',
                                    ),
                                ),
                                'response' => array(
                                    'type' => 'Json',
                                ),
                            ),
                        ),
                        'unit-test-2' => array(
                            'action-1' => array(
                                'name'              => 'action/1',
                                'request'           => array(
                                    'name_in_uri'   => true,
                                    'type'          => 'UrlEncoded',
                                    'method'        => 'GET',
                                    'parameters'    => array(
                                        'param1' => '',
                                        'param2' => '',
                                    ),
                                ),
                                'response' => array(
                                    'type' => 'Xml',
                                ),
                            ),
                        ),
                    ),
                ),
                'wrong_action.request.type'
            ),
        );
    }

    /***************
     *  T E S T S  *
     ***************/
    /**
     * Tests the API Init process
     *
     * @dataProvider providerMockApi
     * @test
     */
    public function testInit($apiName, $config)
    {
        // Getting api mock object
        $apiMock = $this->getAbstractHostApiMock($apiName, $config);

        // Assert if default configuration is equal to the used configuration
        $this->assertEquals($apiMock->getDefaultConfig(), $this->getConfig());

        // Assert if default mock api name is equal to the used mock api name
        $this->assertEquals($apiMock->getApiName(), $this->getApiName());
    }

    /**
     * Tests the Request Object Creation
     *
     * @dataProvider providerMockApi
     * @test
     */
    public function testGetRequestObjectByName($apiName, $config)
    {
        // Getting api mock object
        $apiMock = $this->getAbstractHostApiMock($apiName, $config);

        // Getting actions from config
        $actions = $config['actions'];
        foreach ($actions as $actionFirstName => $actionSet) {
            foreach ($actionSet as $actionSecondName => $actionConfig) {
                // Creating action name from config
                $actionName = $actionFirstName . AbstractHostApi::API_NAME_LEVEL_SEPARATOR . $actionSecondName;
                // Getting Request object
                $request = $apiMock->getActionRequestByName($actionName);
                //Assert that request is an instance of Msl\RemoteHost\Request\AbstractActionRequest
                $this->assertInstanceOf('Msl\RemoteHost\Request\AbstractActionRequest', $request);
                // Getting Request type
                $requestType = $actionConfig['request']['type'];
                $requestClassName = $this->getRequestClassName($requestType);
                //Assert that request is an instance of the found request class name
                $this->assertInstanceOf($requestClassName, $request);
            }
        }
    }

    /**
     * Tests the Request Object Creation
     *
     * @dataProvider providerMockApi
     * @test
     */
    public function testGetRequestObjectByNameException($apiName, $config, $wrongActionName)
    {
        // Getting api mock object
        $apiMock = $this->getAbstractHostApiMock($apiName, $config);

        // Setting expected exception
        $this->setExpectedException('\Msl\RemoteHost\Exception\NotConfiguredActionException');

        // Getting actions from config
        $apiMock->getActionRequestByName($wrongActionName);
    }
} 