<?php
/**
 * Test class to be used to test the common shipment class.
 */

namespace UniCarrier\Test;

class Shipment extends \UniCarrier\Common\Shipment
{
    public function getLabels()
    {
        return ['Label'];
    }
}
