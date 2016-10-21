<?php
/**
 * The interface to be implemented by all manifests
 */

namespace UniCarrier\Common;

use Illuminate\Support\Collection;

/**
 * Interface ManifestInterface
 *
 * @package UniCarrier\Common
 */
interface ManifestInterface extends ObservableInterface
{
    /**
     * Set the Manifest ID.
     *
     * @param string $manifestId
     *
     * @return ManifestInterface
     */
    public function setManifestId(string $manifestId) : ManifestInterface;

    /**
     * Get the manifest ID.
     *
     * @return string|null
     */
    public function getManifestId();

    /**
     * Set the p[rinter to print the manifest on.
     *
     * @param Printer $printer
     *
     * @return ManifestInterface
     */
    public function setManifestPrinter(Printer $printer) : ManifestInterface;

    /**
     * Get the printer to print the manifest on.
     *
     * @return Printer|null
     */
    public function getManifestPrinter();

    /**
     * Set the PDF of the manifest.
     *
     * @param string $manifestPdf
     *
     * @return ManifestInterface
     */
    public function setManifestPdf(string $manifestPdf) : ManifestInterface;

    /**
     * Get a PDF of the manifest.
     *
     * @return string|null
     */
    public function getManifestPdf();

    /**
     * Set the manifest description.
     *
     * @param string $description
     *
     * @return ManifestInterface
     */
    public function setDescription(string $description) : ManifestInterface;

    /**
     * Get the stored description for this manifest.
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set the shipments on this manifest.
     *
     * @param Collection $shipments
     *
     * @return ManifestInterface
     */
    public function setShipments(Collection $shipments) : ManifestInterface;

    /**
     * Get all the shipments on this manifest.
     *
     * @return Collection
     */
    public function getShipments() : Collection;

    /**
     * Add a shipment to this manifest.
     *
     * @param ShipmentInterface $shipment
     *
     * @return ShipmentResponse|ErrorResponse
     */
    public function addShipment(ShipmentInterface $shipment);

    /**
     * Print this manifest.
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function printManifest();

    /**
     * Cancel shipments on this manifest.
     *
     * @param Collection $shipments
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function cancelShipments(Collection $shipments);

    /**
     * Get a shipment using its ID.
     *
     * @param string $shipmentId
     *
     * @return ShipmentInterface
     */
    public function getShipment(string $shipmentId) : ShipmentInterface;

    /**
     * Upload the manifest to the carrier.
     *
     * @return ResponseInterface
     */
    public function upload() : ResponseInterface;

    /**
     * Get the communicator for this manifest.
     *
     * @return CommunicatorInterface
     */
    public function getCommunicator() : CommunicatorInterface;
}
