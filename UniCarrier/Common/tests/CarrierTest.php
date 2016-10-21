<?php
namespace UniCarrier\Test;

use Illuminate\Support\Collection;
use Mockery;

class CarrierTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TestCarrier
     */
    private $carrier;

    public function setUp()
    {
        $this->carrier = Mockery::mock('\UniCarrier\Common\Carrier')->makePartial();
        $this->carrier->initialise();
    }

    public function testConstruct()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);
        $manifest = Mockery::mock('UniCarrier\Common\Manifest');
        $manifest->shouldReceive('addShipment');
        $manifest->shouldReceive('register')->andReturn($manifest);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $this->carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
                'shipments' => [
                    [
                        'id' => 'shipmentId1',
                        'parcels' => [
                            [
                                'weight' => [
                                    'unitOfMeasurement' => 'g',
                                    'value' => 1000
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            $communicator
        );
        $this->assertInternalType('array', $this->carrier->getParameters());
        $this->assertArrayHasKey('id', $this->carrier->getParameters());
        $this->assertEquals('carrierId', $this->carrier->getId());
        $this->assertArrayHasKey('labelPrinter', $this->carrier->getParameters());
        $this->assertInstanceOf('UniCarrier\Common\Printer', $this->carrier->getLabelPrinter());
        $this->assertArrayHasKey('manifestPrinter', $this->carrier->getParameters());
        $this->assertInstanceOf('UniCarrier\Common\Printer', $this->carrier->getmanifestPrinter());
        $this->assertArrayHasKey('manifest', $this->carrier->getParameters());
        $this->assertSame($manifest, $this->carrier->getManifest());
    }

    public function testConstructParcelWithIdAndLabel()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $this->carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'shipments' => [
                    [
                        'id' => 'shipmentId1',
                        'parcels' => [
                            [
                                'id' => 'parcelId',
                                'weight' => [
                                    'unitOfMeasurement' => 'g',
                                    'value' => 1000
                                ],
                                'label' => 'label',
                            ]
                        ]
                    ]
                ]
            ],
            $communicator
        );
        $this->assertInstanceOf('UniCarrier\Test\Manifest', $this->carrier->getManifest());
        $shipment = $this->carrier->getManifest()->getShipment('shipmentId1');
        $this->assertInstanceOf('UniCarrier\Test\Shipment', $shipment);
        $parcels = $shipment->getParcels();
        $this->assertInstanceOf('Illuminate\Support\Collection', $parcels);
        $this->assertEquals(1, $parcels->count());
        $this->assertInstanceOf('UniCarrier\Test\Parcel', $parcels->first());
        $this->assertEquals('parcelId', $parcels->first()->getId());
        $this->assertEquals('label', $parcels->first()->getLabel());
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateWithShipmentWithoutParcelsThrowsException()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $this->carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'shipments' => [
                    [
                        'id' => 'shipmentId1',
                    ]
                ]
            ],
            $communicator
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testCreateWithShipmentWithEmptyParcelsThrowsException()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $this->carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'shipments' => [
                    [
                        'id' => 'shipmentId1',
                        'parcels' => [],
                    ]
                ]
            ],
            $communicator
        );
    }

    public function testGetShortName()
    {
        $this->assertSame('\\'.get_class($this->carrier), $this->carrier->getShortName());
    }

    public function testInitialiseDefaults()
    {
        $defaults = array(
            'password' => 'password', // fixed default type
            'username' => array('joe', 'fred'), // enum default type
        );
        $this->carrier->shouldReceive('getDefaultParameters')->once()
            ->andReturn($defaults);

        $this->carrier->initialise();

        $expected = array(
            'password' => 'password',
            'username' => 'joe',
        );
        $this->assertSame($expected, $this->carrier->getParameters());
    }

    public function testInitializeParameters()
    {
        $this->carrier->shouldReceive('getDefaultParameters')->once()
            ->andReturn(array('password' => 'password'));

        $this->carrier->initialise(array(
            'password' => 'password',
            'unknown' => '42',
        ));

        $this->assertSame(array('password' => 'password'), $this->carrier->getParameters());
    }

    public function testGetDefaultParameters()
    {
        $this->assertInternalType('array', $this->carrier->getDefaultParameters());
        $this->assertArrayHasKey('id', $this->carrier->getDefaultParameters());
        $this->assertArrayHasKey('labelPrinter', $this->carrier->getDefaultParameters());
        $this->assertArrayHasKey('manifestPrinter', $this->carrier->getDefaultParameters());
        $this->assertArrayHasKey('manifest', $this->carrier->getDefaultParameters());
    }

    public function testGetParameters()
    {
        $this->carrier->setTestMode(true);

        $this->assertInternalType('array', $this->carrier->getParameters());
        $this->assertArrayHasKey('testMode', $this->carrier->getParameters());
        $this->assertTrue($this->carrier->getParameters()['testMode']);
    }

    public function testTestMode()
    {
        $this->assertSame($this->carrier, $this->carrier->setTestMode(true));
        $this->assertSame(true, $this->carrier->getTestMode());
    }

    public function testCreateShipment()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $this->carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId'
            ],
            $communicator
        );

        $shipment = $this->carrier->createShipment(
            [
                'id' => 'id',
                'parcels' => [
                    [
                        'weight' => [
                            'unitOfMeasurement' => 'g',
                            'value' => 1000,
                        ]
                    ]
                ]
            ]
        );
        $this->assertInstanceOf('UniCarrier\Test\Shipment', $shipment);
    }

    public function testCreateManifest()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $this->carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'shipments' => [
                    [
                        'id' => 'shipmentId1',
                        'parcels' => [
                            [
                                'weight' => [
                                    'unitOfMeasurement' => 'g',
                                    'value' => 1000
                                ]
                            ]
                        ]
                    ],
                    [
                        'id' => 'shipmentId2',
                        'parcels' => [
                            [
                                'weight' => [
                                    'unitOfMeasurement' => 'g',
                                    'value' => 1000
                                ]
                            ]
                        ]
                    ]
                ],
                'manifestId' => 'manifestId',
            ],
            $communicator
        );

        $this->assertInstanceOf('UniCarrier\Test\Manifest', $this->carrier->getManifest());
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingParametersThrowsException()
    {
        $carrier = new Carrier([]);
    }

    public function testMissingCommunicatorCreatesDefaultCommunicator()
    {
        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'testEndPoint' => 'testEndPoint',
                'liveEndPoint' => 'liveEndPoint',
                'user' => 'user',
                'password' => 'password',
            ]
        );

        $this->assertInstanceOf('UniCarrier\Test\Communicator', $carrier->getCommunicator());
    }

    public function testSetManifestDescription()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'testEndPoint' => 'testEndPoint',
                'liveEndPoint' => 'liveEndPoint',
                'user' => 'user',
                'password' => 'password',
            ],
            $communicator
        );

        $this->assertContains('Manifest for ', $carrier->getManifestDescription());

        $carrier->setManifestDescription('Manifest Description');

        $this->assertEquals('Manifest Description', $carrier->getManifestDescription());
    }

    public function testAddShipmentNoManifest()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'testEndPoint' => 'testEndPoint',
                'liveEndPoint' => 'liveEndPoint',
                'user' => 'user',
                'password' => 'password',
            ],
            $communicator
        );

        $this->assertNull($carrier->getManifest());

        $response = $carrier->addShipment(
            [
                'id' => 'shipmentId1',
                'parcels' => [
                    [
                        'weight' => [
                            'unitOfMeasurement' => 'g',
                            'value' => 1000
                        ]
                    ]
                ]
            ]
        );
        $this->assertTrue($response->isSuccessful());

        $this->assertInstanceOf('UniCarrier\Test\Manifest', $carrier->getManifest());

        $this->assertEquals(1, $carrier->getManifest()->getShipments()->count());

        $this->assertInstanceOf('UniCarrier\Test\Shipment', $carrier->getManifest()->getShipments()->first());
    }

    public function testAddShipmentExistingManifest()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $manifest = new \UniCarrier\Test\Manifest(
            $communicator,
            [
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'testEndPoint' => 'testEndPoint',
                'liveEndPoint' => 'liveEndPoint',
                'user' => 'user',
                'password' => 'password',
            ]
        );

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $this->assertInstanceOf('UniCarrier\Test\Manifest', $carrier->getManifest());

        $this->assertEquals(0, $carrier->getManifest()->getShipments()->count());

        $response = $carrier->addShipment(
            [
                'id' => 'shipmentId1',
                'parcels' => [
                    [
                        'weight' => [
                            'unitOfMeasurement' => 'g',
                            'value' => 1000
                        ]
                    ]
                ]
            ]
        );

        $this->assertTrue($response->isSuccessful());

        $this->assertEquals(1, $carrier->getManifest()->getShipments()->count());

        $this->assertInstanceOf('UniCarrier\Test\Shipment', $carrier->getManifest()->getShipments()->first());
    }

    public function testUpdateShipment()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $shipmentResponse = Mockery::mock('UniCarrier\Common\ShipmentResponse');
        $shipmentResponse->shouldReceive('isSuccessful')->andReturn(true);

        $shipment = Mockery::mock('UniCarrier\Test\Shipment');
        $shipment->shouldReceive('update')->andReturn($shipmentResponse);

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('getShipment')->andReturn($shipment);
        $manifest->shouldReceive('register');

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $response = $carrier->updateShipment(['shipmentId' => 'shipmentId']);

        $this->assertTrue($response->isSuccessful());
    }

    public function testPrintLabels()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $shipmentResponse = Mockery::mock('UniCarrier\Common\ShipmentResponse');
        $shipmentResponse->shouldReceive('isSuccessful')->andReturn(true);

        $shipment = Mockery::mock('UniCarrier\Test\Shipment');
        $shipment->shouldReceive('printLabels')->andReturn($shipmentResponse);

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('getShipment')->andReturn($shipment);
        $manifest->shouldReceive('register');

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $response = $carrier->printLabels('shipmentId');
        $this->assertTrue($response->isSuccessful());
    }

    public function testGetLabels()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $shipment = Mockery::mock('UniCarrier\Test\Shipment');
        $shipment->shouldReceive('getLabels')->andReturn(['label']);

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('getShipment')->andReturn($shipment);
        $manifest->shouldReceive('register');

        $getLabelsRun = false;

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $this->assertFalse($getLabelsRun);

        $labels = $carrier->getLabels('shipmentId');

        $this->assertInternalType('array', $labels);

        $this->assertEquals(1, count($labels));

        $this->assertEquals('label', $labels[0]);
    }

    public function testCancelShipments()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $shipment = Mockery::mock('UniCarrier\Test\Shipment');
        $shipment->shouldReceive('getLabels')->andReturn(['label']);

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('getShipment')->andReturn($shipment);
        $manifest->shouldReceive('register');
        $cancelRun = false;
        $manifest->shouldReceive('cancelShipments')->andReturnUsing(function () use (&$cancelRun) {
            $cancelRun = true;
        });

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $carrier->cancelShipments(['shipmentId']);

        $this->assertTrue($cancelRun);
    }

    public function testUploadManifest()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $uploadRun = false;

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('register');
        $manifest->shouldReceive('upload')->andReturnUsing(function () use (&$uploadRun) {
            $uploadRun = true;
            return Mockery::mock('UniCarrier\Common\ResponseInterface');
        });

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $carrier->uploadManifest();

        $this->assertTrue($uploadRun);
    }

    public function testSetShipmentObserver()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $shipment = Mockery::mock('UniCarrier\Test\Shipment');
        $registerRun = false;
        $shipment->shouldReceive('register')->andReturnUsing(function () use (&$registerRun) {
            $registerRun = true;
        });

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('register');
        $manifest->shouldReceive('getShipments')->andReturn(new Collection([$shipment]));

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $observer = Mockery::mock('Unicarrier\Common\ObserverInterface');

        $carrier->setShipmentObserver($observer);

        $this->assertTrue($registerRun);

        $this->assertSame($observer, $carrier->getShipmentObserver());
    }

    public function testSetManifestObserver()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $registerRun = false;

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('register')->andReturnUsing(function () use (&$registerRun) {
            $registerRun = true;
        });

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $observer = Mockery::mock('Unicarrier\Common\ObserverInterface');

        $carrier->setManifestObserver($observer);

        $this->assertTrue($registerRun);

        $this->assertSame($observer, $carrier->getManifestObserver());
    }

    public function testSetCommunicatorObserver()
    {
        $registerRun = false;

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('register');

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('register')->andReturnUsing(function () use (&$registerRun) {
            $registerRun = true;
        });

        $communicator->shouldReceive('setCarrier')->andReturn($communicator);

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $observer = Mockery::mock('Unicarrier\Common\ObserverInterface');

        $carrier->setCommunicatorObserver($observer);

        $this->assertTrue($registerRun);

        $this->assertSame($observer, $carrier->getCommunicatorObserver());
    }

    public function testPrintManifest()
    {
        $printRun = false;

        $manifest = Mockery::mock('UniCarrier\Test\Manifest');
        $manifest->shouldReceive('register');
        $manifest->shouldReceive('printManifest')->andReturnUsing(function () use (&$printRun) {
            $printRun = true;
        });

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('register');

        $communicator->shouldReceive('setCarrier')->andReturn($communicator);

        $carrier = new TestCarrier(
            [
                'id' => 'carrierId',
                'labelPrinter' => $labelPrinter,
                'manifestPrinter' => $manifestPrinter,
                'manifestId' => 'manifestId',
                'manifest' => $manifest,
            ],
            $communicator
        );

        $carrier->printManifest();

        $this->assertTrue($printRun);
    }
}
