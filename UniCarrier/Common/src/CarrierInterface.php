<?php
/**
 * Interface to be implemented by all Carrier classes
 */

namespace UniCarrier\Common;

use Illuminate\Support\Collection;

/**
 * Interface CarrierInterface
 *
 * @package UniCarrier\Common
 */
interface CarrierInterface
{
    /**
     * Get the short name for the carrier based on the class name
     *
     * @return string
     */
    public function getShortName() : string;

    /**
     * Get the default parameters for the carrier.
     *
     * @return array
     */
    public function getDefaultParameters() : array;

    /**
     * Set the Carrier ID.
     *
     * @param $id
     *
     * @return CarrierInterface
     */
    public function setId($id) : CarrierInterface;

    /**
     * Get the carrier ID.
     *
     * @return string|null
     */
    public function getId();

    /**
     * Set the current manifest for this carrier.
     *
     * @param ManifestInterface $manifest
     *
     * @return CarrierInterface
     */
    public function setManifest(ManifestInterface $manifest) : CarrierInterface;

    /**
     * Get the manifest for this carrier.
     *
     * @return ManifestInterface|null
     */
    public function getManifest();

    /**
     * Set the Label Printer for this carrier.
     *
     * @param Printer $printer
     *
     * @return CarrierInterface
     */
    public function setLabelPrinter(Printer $printer) : CarrierInterface;

    /**
     * Get the label printer for this carrier.
     *
     * @return Printer|null
     */
    public function getLabelPrinter();

    /**
     * Set the Manifest Printer for this carrier.
     *
     * @param Printer $printer
     *
     * @return CarrierInterface
     */
    public function setManifestPrinter(Printer $printer) : CarrierInterface;

    /**
     * Get the Manifest printer for this carrier.
     *
     * @return Printer|null
     */
    public function getManifestPrinter();

    /**
     * Print the current manifest.
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function printManifest();

    /**
     * Return true if using test mode.
     *
     * @return bool
     */
    public function getTestMode() : bool;

    /**
     * Set the test mode.
     *
     * @param $value
     *
     * @return $this|CarrierInterface
     */
    public function setTestMode($value) : CarrierInterface;

    /**
     * Create a shipment.
     *
     * @param array $parameters Must include 'parcels';
     *
     * @return ShipmentInterface
     * @throws \Exception Exception thrown if not at least one parcel is supplied.
     */
    public function createShipment($parameters) : ShipmentInterface;

    /**
     * Create the manifest.
     *
     * @param string     $manifestId
     * @param Collection $shipments
     * @param string     $description
     *
     * @return $this|ManifestInterface
     */
    public function createManifest(
        string $manifestId,
        Collection $shipments,
        string $description
    ) : ManifestInterface;

    /**
     * Add a shipment to the current manifest. If no manifest exists, create a new one.
     *
     * @param array $shipmentData
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function addShipment(array $shipmentData);

    /**
     * Update a shipment.
     *
     * @param array $shipmentData
     *
     * @return ShipmentResponse|ErrorResponse
     */
    public function updateShipment($shipmentData);

    /**
     * Cancel all of the supplied shipments.
     *
     * @param array $shipmentIds
     *
     * @return ErrorResponse|ManifestResponse
     */
    public function cancelShipments(array $shipmentIds);

    /**
     * Print labels for the supplied shipment.
     *
     * @param string $shipmentId
     *
     * @return ShipmentResponse|ErrorResponse
     */
    public function printLabels(string $shipmentId);

    /**
     * Get labels for all parcels on the current manifest.
     *
     * @param string $shipmentId
     *
     * @return array
     */
    public function getLabels(string $shipmentId) : array;

    /**
     * Upload the current manifest.
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function uploadManifest();

    /**
     * Get the communicator for this carrier.
     *
     * @return CommunicatorInterface
     */
    public function getCommunicator() : CommunicatorInterface;

    /**
     * Set the shipment observer
     *
     * @param ObserverInterface $observer
     *
     * @return CarrierInterface
     */
    public function setShipmentObserver(ObserverInterface $observer) : CarrierInterface;

    /**
     * Get the shipment observer.
     *
     * @return ObserverInterface|null
     */
    public function getShipmentObserver();

    /**
     * Set the manifest observer
     *
     * @param ObserverInterface $observer
     *
     * @return CarrierInterface
     */
    public function setManifestObserver(ObserverInterface $observer) : CarrierInterface;

    /**
     * Get the manifest observer.
     *
     * @return ObserverInterface|null
     */
    public function getManifestObserver();

    /**
     * Set the communicator observer
     *
     * @param ObserverInterface $observer
     *
     * @return CarrierInterface
     */
    public function setCommunicatorObserver(ObserverInterface $observer) : CarrierInterface;

    /**
     * Get the communicator observer.
     *
     * @return ObserverInterface|null
     */
    public function getCommunicatorObserver();
}
