<?php

namespace UniCarrier\Test;

use Illuminate\Support\Collection;
use Mockery;
use \PHPUnit\Framework\TestCase;

class ObservableTraitTest extends TestCase
{
    private $observer;

    private $communicator;

    public function setUp()
    {
        parent::setUp();

        $this->observer = Mockery::mock('UniCarrier\Common\ObserverInterface');
        $this->observer->shouldReceive('update');

        $this->communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
    }

    public function testRegisterObserver()
    {
        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $this->communicator,
            [
                'id' => 'shipmentId1',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
            ]
        );
        $shipment->register($this->observer);
        $this->assertInternalType('array', $this->getProperty($shipment, 'observers'));
        $this->assertSame($this->observer, $this->getProperty($shipment, 'observers')[0]);
    }

    public function testDetachObserver()
    {
        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $this->communicator,
            [
                'id' => 'shipmentId1',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
            ]
        );
        $shipment->register($this->observer);
        $this->assertInternalType('array', $this->getProperty($shipment, 'observers'));
        $this->assertSame($this->observer, $this->getProperty($shipment, 'observers')[0]);
        $shipment->detach($this->observer);
        $this->assertInternalType('array', $this->getProperty($shipment, 'observers'));
        $this->assertEquals(0, count($this->getProperty($shipment, 'observers')));
    }

    public function testNotify()
    {
        $observer = Mockery::mock('UniCarrier\Common\ObserverInterface');
        $notifyRun = false;
        $observer->shouldReceive('update')->andReturnUsing(function () use (&$notifyRun) {
            $notifyRun = true;
        });

        $parcel = new Parcel('g', 1000);
        $parcels = new Collection([$parcel]);
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $shipment = new Shipment(
            $this->communicator,
            [
                'id' => 'shipmentId1',
                'labelPrinter' => $labelPrinter,
                'parcels' => $parcels,
            ]
        );

        $shipment->register($observer);

        $this->assertFalse($notifyRun);
        $shipment->notify('slartybartfast', $shipment);
        $this->assertTrue($notifyRun);
    }

    private function getProperty($object, $propertyName)
    {
        $class = new \ReflectionClass($object);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
