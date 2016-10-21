<?php

namespace UniCarrier\Test;

use \PHPUnit\Framework\TestCase;

class ParcelTest extends TestCase
{
    public function testAccessors()
    {
        $parcel = new Parcel('g', 100);

        $this->assertEquals('g', $parcel->getWeightUnits());
        $this->assertEquals(100, $parcel->getWeightValue());
        $this->assertNull($parcel->getId());
        $this->assertNull($parcel->getLabel());
        $this->assertNull($parcel->getStatus());

        $parcel->setWeightUnits('kg');
        $parcel->setWeightValue(1);
        $parcel->setId('id');
        $parcel->setLabel('label');
        $parcel->setStatus('status');

        $this->assertEquals('kg', $parcel->getWeightUnits());
        $this->assertEquals(1, $parcel->getWeightValue());
        $this->assertEquals('id', $parcel->getId());
        $this->assertEquals('label', $parcel->getLabel());
        $this->assertEquals('status', $parcel->getStatus());
    }
}
