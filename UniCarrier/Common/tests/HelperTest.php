<?php

namespace UniCarrier\Test;

use UniCarrier\Common\Helper;
use Mockery;

class HelperTest extends \PHPUnit\Framework\TestCase
{
    public function testCamelCase()
    {
        $this->assertEquals('testCamelCase', Helper::camelCase('test_camel_case'));
    }

    public function testGetClassShortName()
    {
        $shortname =  Helper::getCarrierShortName('\UniCarrier\Test\Carrier');
        $this->assertEquals('Test', $shortname);
        $shortname =  Helper::getCarrierShortName('Test\Carrier');
        $this->assertEquals('\Test\Carrier', $shortname);
        $shortname =  Helper::getCarrierShortName('\Test\Carrier');
        $this->assertEquals('\Test\Carrier', $shortname);
        $shortname =  Helper::getCarrierShortName('\Test');
        $this->assertEquals('\Test', $shortname);
        $shortname =  Helper::getCarrierShortName('Test');
        $this->assertEquals('\Test', $shortname);
    }

    public function testGetCarrierClassName()
    {
        $classname =  Helper::getCarrierClassName('\Custom\Carrier');
        $this->assertEquals('\Custom\Carrier', $classname);
        $classname =  Helper::getCarrierClassName('Custom_Name');
        $this->assertEquals('\UniCarrier\Custom\NameCarrier', $classname);
        $classname =  Helper::getCarrierClassName('Test');
        $this->assertEquals('\UniCarrier\Test\Carrier', $classname);
    }

    public function testGetNamespace()
    {
        $communicator = Mockery::mock('\UniCarrier\Common\CommunicatorInterface');
        $communicator->shouldReceive('setCarrier');
        $communicator->shouldReceive('register')->andReturn($communicator);

        $labelPrinter = Mockery::mock('UniCarrier\Common\Printer');
        $manifestPrinter = Mockery::mock('UniCarrier\Common\Printer');

        $carrierOptions = [
            'id' => 'id',
            'labelPrinter' => $labelPrinter,
            'manifestPrinter' => $manifestPrinter,
            'manifestId' => 'manifestId',
        ];

        $namespace = Helper::getNamespace(new Carrier($carrierOptions, $communicator));
        $this->assertEquals('UniCarrier\Test', $namespace);
    }
}
