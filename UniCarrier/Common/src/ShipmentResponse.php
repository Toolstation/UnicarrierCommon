<?php
/**
 * Response sent on completion of a successful shipment command
 */

namespace UniCarrier\Common;

/**
 * Class ManifestResponse
 *
 * @package UniCarrier\Common
 */
class ShipmentResponse extends SuccessResponse
{
    /**
     * The shipment.
     *
     * @var ShipmentInterface
     */
    private $shipment;

    /**
     * ShipmentResponse constructor.
     *
     * @param array            $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->shipment = $data['shipment'];
    }

    /**
     * Get the manifest.
     *
     * @return ShipmentInterface
     */
    public function getShipment() : ShipmentInterface
    {
        return $this->shipment;
    }
}
