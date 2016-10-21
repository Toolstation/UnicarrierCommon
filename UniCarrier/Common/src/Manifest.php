<?php
/**
 * Base manifest class to be extended by all carriers that use a manifest.
 */

namespace UniCarrier\Common;

use Illuminate\Support\Collection;

/**
 * Class Manifest
 *
 * @package UniCarrier\Common
 */
abstract class Manifest implements ManifestInterface
{
    use CarrierTrait, ObservableTrait;

    /**
     * The communicator for this manifest.
     *
     * @var CommunicatorInterface
     */
    protected $communicator;

    /**
     * The paramters for this manifest.
     * @var Collection
     */
    protected $parameters;

    /**
     * A PDF of the manifest to be used to print it.
     * @var string
     */
    protected $manifestPdf;

    /**
     * Manifest constructor.
     *
     * @param CommunicatorInterface $communicator
     * @param array                 $parameters Must include: manifestId, shipments, description and manifestPrinter
     */
    public function __construct(CommunicatorInterface $communicator, array $parameters = [])
    {
        $this->initialise($parameters);

        $this->communicator = $communicator;
    }

    /**
     * Get the default parameters for a manifest.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'manifestId' => null,
            'shipments' => new Collection(),
            'description' => null,
            'manifestPrinter' => null
        ];
    }

    /**
     * Set the manifest ID.
     *
     * @param string $manifestId
     *
     * @return ManifestInterface
     */
    public function setManifestId(string $manifestId) : ManifestInterface
    {
        return $this->setParameter('manifestId', $manifestId);
    }

    /**
     * Get the Manifest ID.
     *
     * @return string|null
     */
    public function getManifestId()
    {
        return $this->getParameter('manifestId');
    }

    /**
     * Set the printer to be used to print the manifest.
     *
     * @param Printer $printer
     *
     * @return ManifestInterface
     */
    public function setManifestPrinter(Printer $printer) : ManifestInterface
    {
        return $this->setParameter('manifestPrinter', $printer);
    }

    /**
     * Get the printer to be used to print the manifest.
     *
     * @return Printer|null
     */
    public function getManifestPrinter()
    {
        return $this->getParameter('manifestPrinter');
    }

    /**
     * Set the pdf for this manifest.
     *
     * @param string $manifestPdf
     *
     * @return ManifestInterface
     */
    public function setManifestPdf(string $manifestPdf) : ManifestInterface
    {
        return $this->setParameter('manifestPdf', $manifestPdf);
    }

    /**
     * Get the pdf for this manifest.
     *
     * @return string|null
     */
    public function getManifestPdf()
    {
        return $this->getParameter('manifestPdf');
    }

    /**
     * Set the description for this manifest.
     *
     * @param string $description
     *
     * @return ManifestInterface
     */
    public function setDescription(string $description) : ManifestInterface
    {
        return $this->setParameter('description', $description);
    }

    /**
     * Get the description for this manifest.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getParameter('description');
    }

    /**
     * Set the shipments on this manifest.
     *
     * @param Collection $shipments
     *
     * @return ManifestInterface
     */
    public function setShipments(Collection $shipments) : ManifestInterface
    {
        return $this->setParameter('shipments', $shipments);
    }

    /**
     * Get the shipments on this manifest.
     *
     * @return Collection
     */
    public function getShipments() : Collection
    {
        return $this->getParameter('shipments');
    }

    /**
     * Get a shipment from one of those on this manifest.
     *
     * @param string $shipmentId
     *
     * @return ShipmentInterface
     */
    public function getShipment(string $shipmentId) : ShipmentInterface
    {
        return $this->getShipments()->get($shipmentId);
    }

    /**
     * Add a shipment to the manifest.
     *
     * @param ShipmentInterface $shipment
     *
     * @return ManifestResponse
     */
    public function addShipment(ShipmentInterface $shipment) : ManifestResponse
    {
        $this->getParameter('shipments')->put($shipment->getId(), $shipment);
        return new ManifestResponse(['manifest' => $this]);
    }

    /**
     * Print this manifest.
     *
     * @return ManifestResponse
     */
    public function printManifest() : ManifestResponse
    {
        $this->getManifestPrinter()->print($this->getManifestPdf());

        return new ManifestResponse(['manifest' => $this]);
    }

    /**
     * Cancel shipments on this manifest.
     *
     * @param Collection $shipments
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function cancelShipments(Collection $shipments)
    {
        $shipmentIds = [];
        $errors = [];

        $shipments->each(function ($shipment) use (&$shipmentIds, &$errors) {
            /** @var ShipmentInterface $shipment */
            $shipmentIds[] = $shipment->getId();
            $response = $shipment->cancel();
            if (!$response->isSuccessful()) {
                $errors = array_merge($errors, $response->getErrors());
            }
        });

        if (count($errors) > 0) {
            $errorResponse = new ErrorResponse(['errors' => $errors]);
            return $errorResponse;
        }

        $this->getShipments()->forget($shipmentIds);

        return new ManifestResponse(['manifest' => $this]);
    }

    /**
     * Upload the manifest to the carrier.
     *
     * @return ResponseInterface
     */
    abstract public function upload() : ResponseInterface;

    /**
     * Get the communicator to be used for this manifest.
     *
     * @return CommunicatorInterface
     */
    public function getCommunicator() : CommunicatorInterface
    {
        return $this->communicator;
    }
}
