<?php
/**
 * Base carrier class
 */

namespace UniCarrier\Common;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Base carrier class
 *
 * This abstract class should be extended by all carrier classes
 * throughout the UniCarrier system.  It enforces implementation of
 * the CarrierInterface interface and defines various common attibutes
 * and methods that all carriers should have.
 *
 * Example:
 *
 * <code>
 *   // Initialise the carrier
 *   $carrier->initialize(...);
 *
 *   // Get the carrier parameters.
 *   $parameters = $carrier->getParameters();
  * </code>
 *
 * @see CarrierInterface
 */
abstract class Carrier implements CarrierInterface
{
    use CarrierTrait;

    /**
     * The communicator to be used for this carrier.
     *
     * @var CommunicatorInterface
     */
    protected $communicator;

    /**
     * Create a new carrier instance
     *
     * @param array                      $parameters   An array of parameters. Must include default parameters.
     * @param CommunicatorInterface|null $communicator A class used to communicate with the carrier company API
     * @throws \Exception
     */
    public function __construct(array $parameters, CommunicatorInterface $communicator = null)
    {

        $this->initialise($parameters);
        $missingDefaults = Helper::checkRequiredParametersSet(
            $this,
            array_diff(array_keys($this->getDefaultParameters()), ['manifest'])
        );

        if (count($missingDefaults) > 0) {
            throw new \Exception("Missing default parameters :\n" . implode("\n", $missingDefaults));
        }

        if ($communicator === null) {
            $communicator = $this->getDefaultCommunicator($parameters);
        }

        $this->communicator = $communicator->setCarrier($this);

        if (isset($parameters['shipments'])) {
            foreach ($parameters['shipments'] as $shipmentData) {
                $this->addShipment($shipmentData);
            }
        }
    }

    /**
     * Get the default communicator for this carrier.
     *
     * @param array $parameters
     * @return CommunicatorInterface
     */
    private function getDefaultCommunicator(array $parameters) : CommunicatorInterface
    {
        $class = Helper::getNamespace($this) . '\Communicator';
        return new $class($parameters);
    }

    /**
     * Get the short name for the carrier based on the class name
     *
     * @return string
     */
    public function getShortName() : string
    {
        return Helper::getCarrierShortName(get_class($this));
    }

    /**
     * Get the default parameters for the carrier.
     *
     * @return array
     */
    public function getDefaultParameters() : array
    {
        return [
            'id' => null,
            'labelPrinter' => null,
            'manifestPrinter' => null,
            'manifestId' => null,
            'manifest' => null,
        ];
    }

    /**
     * Set the ID for this carrier.
     *
     * @param $id
     *
     * @return CarrierInterface
     */
    public function setId($id) : CarrierInterface
    {
        return $this->setParameter('id', $id);
    }

    /**
     * Get the ID for this carrier.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getParameter('id');
    }

    /**
     * Set the Label Printer for this carrier.
     *
     * @param Printer $printer
     *
     * @return CarrierInterface
     */
    public function setLabelPrinter(Printer $printer) : CarrierInterface
    {
        return $this->setParameter('labelPrinter', $printer);
    }

    /**
     * Get the label printer for this carrier.
     *
     * @return Printer|null
     */
    public function getLabelPrinter()
    {
        return $this->getParameter('labelPrinter');
    }

    /**
     * Set the Manifest Printer for this carrier.
     *
     * @param Printer $printer
     *
     * @return CarrierInterface
     */
    public function setManifestPrinter(Printer $printer) : CarrierInterface
    {
        return $this->setParameter('manifestPrinter', $printer);
    }

    /**
     * Get the Manifest printer for this carrier.
     *
     * @return Printer|null
     */
    public function getManifestPrinter()
    {
        return $this->getParameter('manifestPrinter');
    }

    /**
     * Return true if using test mode.
     *
     * @return bool
     */
    public function getTestMode() : bool
    {
        return $this->getParameter('testMode');
    }

    /**
     * Set the test mode.
     *
     * @param $value
     *
     * @return $this|CarrierInterface
     */
    public function setTestMode($value) : CarrierInterface
    {
        return $this->setParameter('testMode', $value);
    }

    /**
     * Create a shipment.
     *
     * @param array $parameters Must include 'parcels';
     *
     * @return ShipmentInterface
     * @throws \Exception Exception thrown if not at least one parcel is supplied.
     */
    public function createShipment($parameters) : ShipmentInterface
    {
        $shipmentClass = Helper::getNamespace($this) . '\Shipment';
        $parameters['labelPrinter'] = $this->getLabelPrinter();

        $parcels = new Collection();
        $parcelClass = Helper::getNamespace($this) . '\Parcel';
        if (!isset($parameters['parcels'])) {
            throw new \Exception('There must be at least one parcel on a shipment');
        }
        foreach ($parameters['parcels'] as $parcelData) {
            $parcel = new $parcelClass($parcelData['weight']['unitOfMeasurement'], $parcelData['weight']['value']);
            if (isset($parcelData['id'])) {
                $parcel->setId($parcelData['id']);
            }
            if (isset($parcelData['label'])) {
                $parcel->setLabel($parcelData['label']);
            }
            $parcels->push($parcel);
        }

        if ($parcels->count() == 0) {
            throw new \Exception('There must be at least one parcel on a shipment');
        }

        $parameters['parcels'] = $parcels;

        /** @var ShipmentInterface $shipment */
        $shipment = new $shipmentClass($this->communicator, $parameters);
        $shipment->register($this->getShipmentObserver());

        return $shipment;
    }

    /**
     * Create a new manifest.
     *
     * @param string     $manifestId
     * @param Collection $shipments
     * @param string     $description
     *
     * @return ManifestInterface
     */
    public function createManifest(
        string $manifestId,
        Collection $shipments,
        string $description
    ) : ManifestInterface {
        $manifestClass = Helper::getNamespace($this) . '\Manifest';
        $this->setParameter(
            'manifest',
            new $manifestClass(
                $this->getCommunicator(),
                [
                    'manifestId' => $manifestId,
                    'shipments' => $shipments,
                    'description' => $description,
                    'manifestPrinter' => $this->getManifestPrinter()
                ]
            )
        );

        return $this->getManifest()->register($this->getManifestObserver());
    }

    /**
     * Get the current manifest ID.
     *
     * @param string $manifestId
     *
     * @return CarrierInterface
     */
    public function setManifestId(string $manifestId) : CarrierInterface
    {
        return $this->setParameter('manifestId', $manifestId);
    }

    /**
     * Get the current manifest ID.
     *
     * @return string|null
     */
    public function getManifestId()
    {
        return $this->getParameter('manifestId');
    }

    /**
     * Set the manifest description for the current manifest.
     *
     * @param string $manifestDescription
     *
     * @return CarrierInterface
     */
    public function setManifestDescription(string $manifestDescription) : CarrierInterface
    {
        return $this->setParameter('manifestDescription', $manifestDescription);
    }

    /**
     * Get the manifest description for the current manifest. If none has been set, use the current date.
     *
     * @return string|null
     */
    public function getManifestDescription()
    {
        $description = $this->getParameter('manifestDescription');

        if ($description === null) {
            $description = 'Manifest for ' . (new Carbon())->format('d/m/Y');
            $this->setManifestDescription($description);
        }

        return $description;
    }

    /**
     * Set the current manifest for this carrier.
     *
     * @param ManifestInterface $manifest
     *
     * @return CarrierInterface
     */
    public function setManifest(ManifestInterface $manifest) : CarrierInterface
    {
        $manifest->register($this->getManifestObserver());
        return $this->setParameter('manifest', $manifest);
    }

    /**
     * Get the current manifest.
     *
     * @return ManifestInterface|null
     */
    public function getManifest()
    {
        return $this->getParameter('manifest');
    }

    /**
     * Add a shipment to the current manifest. If no manifest exists, create a new one.
     *
     * @param array $shipmentData
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function addShipment(array $shipmentData)
    {
        $shipment = $this->createShipment($shipmentData);

        if ($this->getManifest() === null) {
            $this->createManifest($this->getManifestId(), new Collection(), $this->getManifestDescription());
        }

        return $this->getManifest()->addShipment($shipment);
    }

    /**
     * Update a shipment.
     *
     * @param array $shipmentData
     *
     * @return ShipmentResponse|ErrorResponse
     */
    public function updateShipment($shipmentData)
    {
        $shipment = $this->getManifest()->getShipment($shipmentData['shipmentId']);
        return $shipment->update($shipmentData);
    }

    /**
     * Cancel all of the supplied shipments.
     *
     * @param array $shipmentIds
     *
     * @return ErrorResponse|ManifestResponse
     */
    public function cancelShipments(array $shipmentIds)
    {
        $shipments = new Collection();
        foreach ($shipmentIds as $shipmentId) {
            $shipments->push($this->getManifest()->getShipment($shipmentId));
        }

        return $this->getManifest()->cancelShipments($shipments);
    }

    /**
     * Print labels for the supplied shipment.
     *
     * @param string $shipmentId
     *
     * @return ShipmentResponse|ErrorResponse
     */
    public function printLabels(string $shipmentId)
    {
        return $this->getManifest()->getShipment($shipmentId)->printLabels();
    }

    /**
     * Get labels for all parcels on the current manifest.
     *
     * @param string $shipmentId
     *
     * @return array
     */
    public function getLabels(string $shipmentId) : array
    {
        return $this->getManifest()->getShipment($shipmentId)->getLabels();
    }

    /**
     * Upload the current manifest.
     *
     * @return ResponseInterface|ErrorResponse
     */
    public function uploadManifest()
    {
        return $this->getManifest()->upload();
    }

    /**
     * Get the communicator for this carrier.
     *
     * @return CommunicatorInterface
     */
    public function getCommunicator() : CommunicatorInterface
    {
        return $this->communicator;
    }

    /**
     * Set the shipment observer
     *
     * @param ObserverInterface $observer
     *
     * @return CarrierInterface
     */
    public function setShipmentObserver(ObserverInterface $observer) : CarrierInterface
    {
        if ($this->getManifest() instanceof ManifestInterface) {
            $this->getManifest()->getShipments()->each(function ($shipment) use ($observer) {
                $shipment->register($observer);
            });
        }
        return $this->setParameter('shipmentObserver', $observer);
    }

    /**
     * Get the shipment observer.
     *
     * @return ObserverInterface|null
     */
    public function getShipmentObserver()
    {
        return $this->getParameter('shipmentObserver');
    }

    /**
     * Set the manifest observer
     *
     * @param ObserverInterface $observer
     *
     * @return CarrierInterface
     */
    public function setManifestObserver(ObserverInterface $observer) : CarrierInterface
    {
        if ($this->getManifest() instanceof ManifestInterface) {
            $this->getManifest()->register($observer);
        }
        return $this->setParameter('manifestObserver', $observer);
    }

    /**
     * Get the manifest observer.
     *
     * @return ObserverInterface|null
     */
    public function getManifestObserver()
    {
        return $this->getParameter('manifestObserver');
    }

    /**
     * Set the communicator observer
     *
     * @param ObserverInterface $observer
     *
     * @return CarrierInterface
     */
    public function setCommunicatorObserver(ObserverInterface $observer) : CarrierInterface
    {
        $this->getCommunicator()->register($observer);
        return $this->setParameter('communicatorObserver', $observer);
    }

    /**
     * Get the communicator observer.
     *
     * @return ObserverInterface|null
     */
    public function getCommunicatorObserver()
    {
        return $this->getParameter('communicatorObserver');
    }

    /**
     * Print the current manifest.
     *
     * @return ManifestResponse|ErrorResponse
     */
    public function printManifest()
    {
        return $this->getManifest()->printManifest();
    }
}
