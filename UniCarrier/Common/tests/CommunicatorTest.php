<?php

namespace UniCarrier\Test;

use Mockery;

class CommunicatorTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $communicatorOptions = [
            'testEndPoint' => 'testEndPoint',
            'liveEndPoint' => 'liveEndPoint',
            'user' => 'user',
            'password' => 'password',
        ];

        $communicator = new Communicator($communicatorOptions);

        $this->assertInstanceOf('UniCarrier\Test\Communicator', $communicator);
        $this->assertFalse($communicator->getTestMode());
        $this->assertEquals('testEndPoint', $communicator->getTestEndPoint());
        $this->assertEquals('liveEndPoint', $communicator->getLiveEndPoint());
        $this->assertEquals('user', $communicator->getUser());
        $this->assertEquals('password', $communicator->getPassword());
    }

    public function testSettersAndGetters()
    {
        $communicatorOptions = [
            'testEndPoint' => 'testEndPoint',
            'liveEndPoint' => 'liveEndPoint',
            'user' => 'user',
            'password' => 'password',
        ];

        $communicator = new Communicator($communicatorOptions);
        $communicator->setTestEndPoint('test end point');
        $this->assertEquals('test end point', $communicator->getTestEndPoint());
        $communicator->setLiveEndPoint('live end point');
        $this->assertEquals('live end point', $communicator->getLiveEndPoint());
        $this->assertFalse($communicator->getTestMode());
        $this->assertEquals('live end point', $communicator->getEndPoint());
        $communicator->setTestMode(true);
        $this->assertTrue($communicator->getTestMode());
        $this->assertEquals('test end point', $communicator->getEndPoint());
        $communicator->setUser('user');
        $this->assertEquals('user', $communicator->getUser());
        $communicator->setPassword('password');
        $this->assertEquals('password', $communicator->getPassword());
        $carrier = Mockery::mock('UniCarrier\Common\CarrierInterface');
        $communicator->setCarrier($carrier);
        $this->assertSame($carrier, $communicator->getCarrier());
        $communicator->setTestMode(true);
        $this->assertTrue($communicator->getTestMode());
        $communicator->setTestMode(false);
        $this->assertFalse($communicator->getTestMode());

        $defaultOptions = $communicator->getDefaultParameters();
        $this->assertInternalType('array', $defaultOptions);
        $this->assertArrayHasKey('testMode', $defaultOptions);
        $this->assertFalse($defaultOptions['testMode']);
        $this->assertArrayHasKey('testEndPoint', $defaultOptions);
        $this->assertNull($defaultOptions['testEndPoint']);
        $this->assertArrayHasKey('liveEndPoint', $defaultOptions);
        $this->assertNull($defaultOptions['liveEndPoint']);
        $this->assertArrayHasKey('user', $defaultOptions);
        $this->assertNull($defaultOptions['user']);
        $this->assertArrayHasKey('password', $defaultOptions);
        $this->assertNull($defaultOptions['password']);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingParameterThrowsException()
    {
        $communicator = new Communicator([]);
    }
}
