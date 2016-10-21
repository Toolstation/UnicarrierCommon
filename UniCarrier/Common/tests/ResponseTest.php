<?php

namespace UniCarrier\Test;

use Mockery;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $request = Mockery::mock('UniCarrier\Common\RequestInterface');
        $response = new Response([]);
        $this->assertInstanceOf('UniCarrier\Test\Response', $response);
        $this->assertInstanceOf('Carbon\Carbon', $response->getTimestamp());
        $this->assertNull($response->getTransactionId());
    }

    public function testSetTransactionId()
    {
        $request = Mockery::mock('UniCarrier\Common\RequestInterface');
        $response = new Response([]);
        $response->setTransactionId('transactionId');
        $this->assertEquals('transactionId', $response->getTransactionId());
    }

    public function testSuccessResponse()
    {
        $successResponse = new SuccessResponse(['warnings' => ['warning1', 'warning2']]);
        $this->assertContains('warning1', $successResponse->getWarnings());
        $this->assertContains('warning2', $successResponse->getWarnings());
        $this->assertEquals("Warnings:\nwarning1\nwarning2", $successResponse->getMessage());
    }
}
