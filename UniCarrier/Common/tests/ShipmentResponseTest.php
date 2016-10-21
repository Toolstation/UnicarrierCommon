<?php

namespace Unicarrier\Test;

use Mockery;
use PHPUnit\Framework\TestCase;
use UniCarrier\Common\ShipmentResponse;

class ShipmentResponseTest extends TestCase
{
    public function testGetters()
    {
        $shipment = Mockery::mock('UniCarrier\Common\Shipment');

        $shipmentResponse = new ShipmentResponse(['shipment' => $shipment]);

        $this->assertSame($shipment, $shipmentResponse->getShipment());
    }
}
