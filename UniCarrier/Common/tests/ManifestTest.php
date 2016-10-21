<?php

namespace UniCarrier\Test;

use Illuminate\Support\Collection;
use Mockery;

class ManifestTest extends \PHPUnit\Framework\TestCase
{
    private $manifestOptions;

    private $communicator;

    public function setUp()
    {
        parent::setUp();
        $this->manifestOptions = [
        ];
        $this->communicator = Mockery::mock('UniCarrier\Common\Communicator');
    }

    public function testConstruct()
    {
        $manifest = new Manifest($this->communicator, $this->manifestOptions);
        $this->assertInstanceOf('UniCarrier\Test\Manifest', $manifest);
        $this->assertNull($manifest->getManifestId());
        $this->assertInstanceOf('Illuminate\Support\Collection', $manifest->getShipments());
        $this->assertNull($manifest->getDescription());
        $this->assertNull($manifest->getManifestPrinter());
    }

    public function testSettersAndGetters()
    {
        $manifest = new Manifest($this->communicator, $this->manifestOptions);
        $this->assertSame($this->communicator, $manifest->getCommunicator());
        $manifest->setManifestId('manifestId');
        $this->assertEquals('manifestId', $manifest->getManifestId());
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifest->setManifestPrinter($manifestPrinter);
        $this->assertInstanceOf('UniCarrier\Common\Printer', $manifest->getManifestPrinter());
        $manifest->setManifestPdf('manifestPdf');
        $this->assertEquals('manifestPdf', $manifest->getManifestPdf());
        $manifest->setDescription('manifest description');
        $this->assertEquals('manifest description', $manifest->getDescription());
        $shipment = Mockery::mock('UniCarrier\Common\Shipment');
        $shipment->shouldReceive('getLabels')->andReturn(['label']);
        $manifest->setShipments(new Collection(['shipmentId' => $shipment]));
        $this->assertInstanceOf('Illuminate\Support\Collection', $manifest->getShipments());
        $this->assertEquals(1, $manifest->getShipments()->count());
        $this->assertSame($shipment, $manifest->getShipments()->first());
        $this->assertSame($shipment, $manifest->getShipment('shipmentId'));
    }

    public function testAddShipment()
    {
        $manifest = new Manifest($this->communicator, $this->manifestOptions);
        $shipment = Mockery::mock('UniCarrier\Common\Shipment');
        $shipment->shouldReceive('getId')->andReturn('shipmentId');
        $this->assertEquals(0, $manifest->getShipments()->count());
        $response = $manifest->addShipment($shipment);
        $this->assertInstanceOf('UniCarrier\Common\ManifestResponse', $response);
        $this->assertSame($manifest, $response->getManifest());
        $this->assertEquals(1, $manifest->getShipments()->count());
        $this->assertSame($shipment, $manifest->getShipments()->first());
    }

    public function testCancelShipments()
    {
        $manifest = new Manifest($this->communicator, $this->manifestOptions);
        $cancelResponse = Mockery::mock('UniCarrier\Common\ShipmentResponse');
        $cancelResponse->shouldReceive('isSuccessful')->andReturn(true);
        $shipment1 = Mockery::mock('UniCarrier\Common\Shipment');
        $shipment1->shouldReceive('getId')->andReturn('shipment1');
        $shipment1->shouldReceive('cancel')->andReturn($cancelResponse);
        $shipment2 = Mockery::mock('UniCarrier\Common\Shipment');
        $shipment2->shouldReceive('getId')->andReturn('shipment2');
        $shipment2->shouldReceive('cancel')->andReturn($cancelResponse);
        $manifest->setShipments(new Collection(['shipment1' => $shipment1, 'shipment2' => $shipment2]));
        $this->assertEquals(2, $manifest->getShipments()->count());
        $manifest->cancelShipments(new Collection([$shipment2]));
        $this->assertEquals(1, $manifest->getShipments()->count());
        $this->assertSame($shipment1, $manifest->getShipments()->first());

        $errorResponse = Mockery::mock('UniCarrier\Common\ErrorResponse');
        $errorResponse->shouldReceive('isSuccessful')->andReturn(false);
        $errorResponse->shouldReceive('getErrors')->andReturn(['Error message']);
        $shipment3 = Mockery::mock('UniCarrier\Common\Shipment');
        $shipment3->shouldReceive('getId')->andReturn('shipment3');
        $shipment3->shouldReceive('cancel')->andReturn($errorResponse);
        $manifest->cancelShipments(new Collection([$shipment3]));
        $response = $manifest->cancelShipments(new Collection([$shipment3]));
        $this->assertInstanceOf('UniCarrier\Common\ErrorResponse', $response);
    }

    public function testPrintManifest()
    {
        $manifestPrinter = Mockery::mock('Unicarrier\Common\Printer');
        $printCalled = false;
        $manifestPrinter->shouldReceive('print')->andReturnUsing(function () use (&$printCalled) {
            $printCalled = true;
        });
        $manifest = new Manifest($this->communicator, $this->manifestOptions);
        $manifest->setManifestPdf('manifestPdf');
        $manifest->setManifestPrinter($manifestPrinter);
        $manifest->printManifest();
        $this->assertTrue($printCalled);
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}
