<?php

namespace UniCarrier\Test;

use PHPUnit\Framework\TestCase;
use UniCarrier\Common\Printer;

class PrinterTest extends TestCase
{
    public function testConstrcutor()
    {
        $printer = new Printer('test command');
        $class = new \ReflectionClass($printer);
        $property = $class->getProperty('printCommand');
        $property->setAccessible(true);

        $this->assertEquals('test command', $property->getValue($printer));
    }
}
