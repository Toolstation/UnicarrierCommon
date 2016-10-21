<?php
/**
 * The shipment interface. To be implemented by all shipment classes.
 */

namespace UniCarrier\Common;

use Illuminate\Support\Collection;

/**
 * Interface ShipmentInterface
 *
 * @package UniCarrier\Common
 */
interface ShipmentInterface extends ObservableInterface
{
    /**
     * Set the ID of the shipment.
     *
     * @param $id
     *
     * @return ShipmentInterface
     */
    public function setId($id) : ShipmentInterface;

    /**
     * Get the ID of the shipment.
     *
     * @return string|null
     */
    public function getId();

    /**
     * Set the parcels on this shipment.
     *
     * @param Collection $parcels
     *
     * @return ShipmentInterface
     */
    public function setParcels(Collection $parcels) : ShipmentInterface;

    /**
     * Get the parcels on this shipment.
     *
     * @return Collection
     */
    public function getParcels() : Collection;

    /**
     * Set the printer to be used to print labels.
     *
     * @param Printer $printer
     *
     * @return ShipmentInterface
     */
    public function setLabelPrinter(Printer $printer) : ShipmentInterface;

    /**
     * Get the printer to be used to print labels.
     *
     * @return Printer|null
     */
    public function getLabelPrinter();

    /**
     * Cancel the shipment.
     *
     * @return void
     */
    public function cancel();

    /**
     * Get the label for the shipment as a PDF.
     *
     * @return array|null
     */
    public function getLabels();

    /**
     * Print labels for this shipment;
     *
     * @return ShipmentResponse|ErrorResponse
     */
    public function printLabels();

    /**
     * Get the communicator for this shipment.
     *
     * @return CommunicatorInterface
     */
    public function getCommunicator() : CommunicatorInterface;
}
