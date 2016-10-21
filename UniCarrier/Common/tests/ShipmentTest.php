<?php

namespace UniCarrier\Test;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mockery;

class ShipmentTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $communicator,
            [
                'id' => 'shipmentId1',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
            ]
        );
        $this->assertInstanceOf('UniCarrier\Test\Shipment', $shipment);
        $allParameters = $shipment->getAllParameters();
        $this->assertInternalType('array', $allParameters);
        $this->assertEquals(3, count($allParameters));
        $this->assertArrayHasKey('id', $allParameters);
        $this->assertArrayHasKey('labelPrinter', $allParameters);
        $this->assertArrayHasKey('parcels', $allParameters);
    }

    public function testGettersAndSetters()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $communicator,
            [
                'id' => 'shipmentId1',
                'shippingDate' => Carbon::createFromFormat('Y-m-d', '2016-08-08'),
                'buildingName' => 'building name',
                'buildingNumber' => '1',
                'addressLine1' => 'address line 1',
                'addressLine2' => 'address line 2',
                'addressLine3' => 'address line 3',
                'postTown' => 'post town',
                'postcode' => 'postcode',
                'country' => 'GB',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
                'serviceEnhancements' => [1],
            ]
        );
        $shipment->setId('shipmentId');
        $this->assertEquals('shipmentId', $shipment->getId());
        $shipment->setLabelPrinter($labelPrinter);
        $this->assertSame($labelPrinter, $shipment->getLabelPrinter());
        $this->assertSame($communicator, $shipment->getCommunicator());
        $this->assertInstanceOf('Illuminate\Support\Collection', $shipment->getParcels());
        $this->assertSame($parcels, $shipment->getParcels());
        $this->assertInstanceOf('Carbon\Carbon', $shipment->getShippingDate());
        $this->assertEquals('building name', $shipment->getBuildingName());
        $this->assertEquals('1', $shipment->getBuildingNumber());
        $this->assertEquals('address line 1', $shipment->getAddressLine1());
        $this->assertEquals('address line 2', $shipment->getAddressLine2());
        $this->assertEquals('address line 3', $shipment->getAddressLine3());
        $this->assertEquals('post town', $shipment->getPostTown());
        $this->assertEquals('postcode', $shipment->getPostcode());
        $this->assertEquals('GB', $shipment->getCountry());
        $this->assertInternalType('array', $shipment->getServiceEnhancements());
        $this->assertEquals(1, $shipment->getServiceEnhancements()[0]);
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingDefaultParamThrowsException()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $shipment = new Shipment($communicator, []);
    }

    public function testPrintLabelLabelIsNull()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $communicator,
            [
                'id' => 'shipmentId1',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
            ]
        );
        $printCalled = false;
        $labelPrinter->shouldReceive('print')->andReturnUsing(function () use (&$printCalled) {
            $printCalled = true;
        });

        $shipment->printLabels();
        $this->assertTrue($printCalled);
    }

    public function testPrintLabelLabelIsNotNull()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $communicator,
            [
                'id' => 'shipmentId1',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
            ]
        );
        $label = $shipment->getLabels();
        $this->assertNotNull($label);
        $printCalled = false;
        $labelPrinter->shouldReceive('print')->andReturnUsing(function () use (&$printCalled) {
            $printCalled = true;
        });

        $shipment->printLabels();
        $this->assertTrue($printCalled);
    }

    public function testUpdate()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $communicator,
            [
                'recipientName' => 'recipient',
                'id' => 'shipmentId1',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
            ]
        );
        $this->assertEquals('recipient', $shipment->getRecipientName());

        $shipment->update(['recipientName' => 'New Name']);

        $this->assertEquals('New Name', $shipment->getRecipientName());
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
