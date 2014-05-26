<?php

namespace Msl\Tests\RemoteHost;

use Msl\RemoteHost\Request\AbstractActionRequest;

/**
 * RemoteHost Test Case
 *
 * @category  RemoteHost
 * @package   Msl\Tests\RemoteHost
 * @author    "Marco Spallanzani" <mslib.code@gmail.com>
 */
class RemoteHostTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests config
     *
     * @var array
     */
    protected $config;

    /**
     * Tests api name
     *
     * @var string
     */
    protected $apiName;

    /**
     * Returns a mock object for Msl\RemoteHost\Api\AbstractHostApi class
     *
     * @return mixed
     */
    protected function getAbstractHostApiMock($apiName, array $config)
    {
        // Setting default mock config
        $this->setConfig($config);
        $this->setApiName($apiName);

        // Creating the mock object
        $mock = $this->getMockForAbstractClass(
            'Msl\RemoteHost\Api\AbstractHostApi',
            array(
                $apiName,
                $config,
            )
        );

        // Setting behaviour for abstract method
        $mock
            ->expects($this->any())
            ->method('getDefaultConfig')
            ->will($this->returnValue($config));

        // Returning mock object
        return $mock;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $apiName
     */
    public function setApiName($apiName)
    {
        $this->apiName = $apiName;
    }

    /**
     * @return string
     */
    public function getApiName()
    {
        return $this->apiName;
    }

    public function getRequestClassName($requestType)
    {
        // We check if the request type is a valid namespace
        $className = null;
        if (class_exists($requestType)) {
            $className = $requestType;
        } elseif (class_exists(AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType)) {
            // If still not found, we add to the string the default namespace
            $className = AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType;
        } elseif (class_exists(AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType . AbstractActionRequest::REQUEST_CLASSNAME_SUFFIX)) {
            // If still not found, we add to the string a default class name suffix and the default namespace
            $className = AbstractActionRequest::REQUEST_NAMESPACE . '\\' . $requestType . AbstractActionRequest::REQUEST_CLASSNAME_SUFFIX;
        }
        return $className;
    }
}