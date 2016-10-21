<?php

namespace UniCarrier\Test;

use \PHPUnit\Framework\TestCase;
use Mockery;
use UniCarrier\Common\SoapCommunicator;

class SoapCommunicatorTest extends TestCase
{
    /**
     * @var SoapCommunicator
     */
    private $soapCommunicator;

    public function setUp()
    {
        parent::setUp();

        $communicatorOptions = [
            'testEndPoint' => 'testEndPoint',
            'liveEndPoint' => 'liveEndPoint',
            'user' => 'user',
            'password' => 'password',
        ];

        $this->soapCommunicator = new TestSoapCommunicator($communicatorOptions);
    }

    public function testGetClientOptions()
    {
        $this->assertInternalType('array', $this->soapCommunicator->getClientOptions());
        $this->assertEmpty($this->soapCommunicator->getClientOptions());
    }

    public function testGetClientClientNull()
    {
        $client = $this->runProtectedMethod($this->soapCommunicator, 'getParameter', ['client']);
        $this->assertNull($client);

        $client = $this->soapCommunicator->getClient();

        $this->assertInstanceOf('SoapClient', $client);

        $this->assertSame($client, $this->runProtectedMethod($this->soapCommunicator, 'getParameter', ['client']));
    }

    public function testGetClientNotNull()
    {
        $client = Mockery::mock('SoapClient');
        $this->runProtectedMethod($this->soapCommunicator, 'setParameter', ['client', $client]);
        $this->assertSame($client, $this->runProtectedMethod($this->soapCommunicator, 'getParameter', ['client']));

        $newClient = $this->soapCommunicator->getClient();

        $this->assertSame($client, $newClient);
    }

    /**
     * @expectedException \Exception
     */
    public function testSendDataInvalidClientThrowsException()
    {
        $client = 'client';

        $this->runProtectedMethod($this->soapCommunicator, 'setParameter', ['client', $client]);
        $this->assertSame($client, $this->runProtectedMethod($this->soapCommunicator, 'getParameter', ['client']));

        $this->soapCommunicator->sendData([]);
    }

    public function testSendDataClientThrowsException()
    {
        $client = Mockery::mock('SoapClient');
        $client->shouldReceive('__soapCall')->andThrow(new \Exception('Exception'));

        $this->runProtectedMethod($this->soapCommunicator, 'setParameter', ['client', $client]);
        $this->assertSame($client, $this->runProtectedMethod($this->soapCommunicator, 'getParameter', ['client']));

        $result = $this->soapCommunicator->sendData(['soapFunction' => 'test', 'payload' => []]);

        $this->assertInstanceOf('Exception', $result);
        $this->assertEquals('Exception', $result->getMessage());
    }

    public function testSendDataSucceeds()
    {
        $client = Mockery::mock('SoapClient');
        $client->shouldReceive('__soapCall')->andReturn('Success');

        $this->runProtectedMethod($this->soapCommunicator, 'setParameter', ['client', $client]);
        $this->assertSame($client, $this->runProtectedMethod($this->soapCommunicator, 'getParameter', ['client']));

        $result = $this->soapCommunicator->sendData(['soapFunction' => 'test', 'payload' => []]);

        $this->assertEquals('Success', $result);
    }


    public function runProtectedMethod($object, $method, array $args)
    {
        $class = new \ReflectionClass($object);
        $method = $class->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $args);
    }
}
