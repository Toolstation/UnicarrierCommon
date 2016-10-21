<?php

namespace UniCarrier\Test;

use Mockery;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $request = new Request($communicator);
        $this->assertInstanceOf('UniCarrier\Test\Request', $request);
        $this->assertNull($request->getTransactionId());
        $this->assertNull($request->getCommand());
    }

    public function testSettersAndGetters()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $request = new Request($communicator);
        $request->setCommand('command');
        $this->assertEquals('command', $request->getCommand());
        $request->setTransactionId('transactionId');
        $this->assertEquals('transactionId', $request->getTransactionId());
        $this->assertSame($communicator, $request->getCommunicator());
        $response = Mockery::mock('UniCarrier\Common\ResponseInterface');
        $request->setResponse($response);
        $this->assertSame($response, $request->getResponse());
    }

    public function testSend()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $request = new Request($communicator);

        $response = $request->send();
        $this->assertInstanceOf('UniCarrier\Common\ResponseInterface', $response);
    }
}
