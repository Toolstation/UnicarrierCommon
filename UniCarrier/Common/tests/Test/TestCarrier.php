<?php
namespace UniCarrier\Test;

/**
 * Test class to test abstract Carrier class
 */
class TestCarrier extends \UniCarrier\Common\Carrier
{
    public function getName()
    {
        return 'Mock Gateway Implementation';
    }
}
