<?php
namespace UniCarrier\Test;

use UniCarrier\Common\CarrierFactory;
use Mockery;

class CarrierFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateCarrier()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrierOptions = [
            'id' => 'carrierId',
            'labelPrinter' => $labelPrinter,
            'manifestPrinter' => $manifestPrinter,
            'manifestId' => 'manifestId',
            'shipments' => [
                [
                    'id' => 'sipmentId1',
                    'parcels' => [
                        [
                            'weight' => [
                                'unitOfMeasurement' => 'g',
                                'value' => 1000,
                            ]

                        ]
                    ]
                ]
            ],
        ];

        $carrier = CarrierFactory::createCarrier('Test', $carrierOptions, $communicator);

        $this->assertInstanceOf('UniCarrier\Test\Carrier', $carrier);
    }

    /**
     * @expectedException \RunTimeException
     */
    public function testNonExistantThrowsException()
    {
        $communicator = Mockery::mock('UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');

        $carrierOptions = [
            'id' => 'carrierId',
            'labelPrinter' => 'labelPrinter',
            'manifestPrinter' => 'manifestPrinter',
            'manifestId' => 'manifestId',
            'shipments' => [
                'parcels' => [
                    [
                        'weight' => [
                            'unitOfMeasurement' => 'g',
                            'value' => 1000,
                        ]

                    ]
                ]
            ],
        ];

        $carrier = CarrierFactory::createCarrier('Slartybartfast', $carrierOptions, $communicator);
    }
}
