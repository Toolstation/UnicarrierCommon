<?php
/**
 * The base shipment class. To be extended by all shipment classes
 */

namespace UniCarrier\Common;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Shipment
 *
 * @package UniCarrier\Common
 */
abstract class Shipment implements ShipmentInterface
{
    use CarrierTrait, ObservableTrait;

    /**
     * The Communicator for this carrier.
     *
     * @var CommunicatorInterface
     */
    protected $communicator;

    /**
     * The parameters for this shipment.
     *
     * @var Collection
     */
    protected $parameters;

    /**
     * Create a new shipment instance
     *
     * @param CommunicatorInterface $communicator A class used to communicate with the carrier company API
     * @param array $parameters The parameters to use to set up the shipment.
     * @throws \Exception Exception thrown if an expected parameter is not supplied.
     */
    public function __construct(
        CommunicatorInterface $communicator,
        array $parameters = []
    ) {
        $this->communicator = $communicator;
        $this->initialise($parameters);
        $missingDefaults = Helper::checkRequiredParametersSet(
            $this,
            array_diff(array_keys($this->getDefaultParameters()), ['manifest'])
        );

        if (count($missingDefaults) > 0) {
            throw new \Exception("Missing default parameters :\n" . implode("\n", $missingDefaults));
        }
    }

    /**
     * Get an array of default parameters for a shipment.
     *
     * @return array
     */
    public function getDefaultParameters() : array
    {
        return [
            'id' => null,
            'labelPrinter' => null,
        ];
    }

    /**
     * Get all the parameters for this shipment.
     *
     * @return array
     */
    public function getAllParameters() : array
    {
        return $this->parameters->toArray();
    }

    /**
     * Set the ID for this shipment.
     *
     * @param $id
     *
     * @return ShipmentInterface
     */
    public function setId($id) : ShipmentInterface
    {
        return $this->setParameter('id', $id);
    }

    /**
     * Get the ID of the shipment.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getParameter('id');
    }

    /**
     * Set the parcels on this shipment.
     *
     * @param Collection $parcels
     *
     * @return ShipmentInterface
     */
    public function setParcels(Collection $parcels) : ShipmentInterface
    {
        return $this->setParameter('parcels', $parcels);
    }

    /**
     * Get the parcels on this shipment.
     *
     * @return Collection
     */
    public function getParcels() : Collection
    {
        $parcels = $this->getParameter('parcels');

        return ($parcels === null) ? new Collection() : $parcels;
    }

    /**
     * Set the label printer for this shipment.
     *
     * @param Printer $printer
     *
     * @return ShipmentInterface
     */
    public function setLabelPrinter(Printer $printer) : ShipmentInterface
    {
        return $this->setParameter('labelPrinter', $printer);
    }

    /**
     * Get the label printer.
     *
     * @return Printer|null
     */
    public function getLabelPrinter()
    {
        return $this->getParameter('labelPrinter');
    }

    /**
     * Cancel the shipment.
     *
     * @return void
     */
    public function cancel()
    {
    } // @codeCoverageIgnore

    /**
     * Get the labels for the shipment as PDFs.
     *
     * @return array|null
     */
    abstract public function getLabels();

    /**
     * Print labels for this shipment;
     *
     * @return ResponseInterface
     */
    public function printLabels() : ResponseInterface
    {
        try {
            foreach ($this->getLabels() as $label) {
                $this->getLabelPrinter()->print($label);
            }
            //@codeCoverageIgnoreStart
        } catch (\Exception $exception) {
            return new ErrorResponse(['shipment' => $this, 'errors' => [$exception->getMessage()]]);
        }
        //@codeCoverageIgnoreEnd

        return new ShipmentResponse(['shipment' => $this]);
    }

    /**
     * Get the communicator.
     *
     * @return CommunicatorInterface
     */
    public function getCommunicator() : CommunicatorInterface
    {
        return $this->communicator;
    }

    /**
     * Get the date of this shipment.
     *
     * @return Carbon|null
     */
    public function getShippingDate()
    {
        return $this->getParameter('shippingDate');
    }

    /**
     * Set the date of this shipment.
     *
     * @param Carbon $shippingDate
     *
     * @return ShipmentInterface
     */
    public function setShippingDate($shippingDate) : ShipmentInterface
    {
        return $this->setParameter('shippingDate', $shippingDate);
    }

    /**
     * Set the recipient's name.
     *
     * @param string $name
     *
     * @return ShipmentInterface
     */
    public function setRecipientName(string $name) : ShipmentInterface
    {
        return $this->setParameter('recipientName', $name);
    }

    /**
     * Get the recipient's name.
     *
     * @return string
     */
    public function getRecipientName() : string
    {
        return $this->getParameter('recipientName');
    }

    /**
     * Set the recipient's building name portion of their address.
     *
     * @param string $buildingName
     *
     * @return ShipmentInterface
     */
    public function setBuildingName(string $buildingName) : ShipmentInterface
    {
        return $this->setParameter('buildingName', $buildingName);
    }

    /**
     * Get the recipient's building name portion of their address.
     *
     * @return string|null
     */
    public function getBuildingName()
    {
        return $this->getParameter('buildingName');
    }

    /**
     * Set the recipient's building number portion of their address.
     *
     * @param string $buildingNumber
     *
     * @return ShipmentInterface
     */
    public function setBuildingNumber(string $buildingNumber) : ShipmentInterface
    {
        return $this->setParameter('buildingNumber', $buildingNumber);
    }

    /**
     * Get the recipient's building number portion of their address.
     *
     * @return string|null
     */
    public function getBuildingNumber()
    {
        return $this->getParameter('buildingNumber');
    }

    /**
     * Set the recipient's address line 1 portion of their address.
     *
     * @param string $addressLine1
     *
     * @return ShipmentInterface
     */
    public function setAddressLine1(string $addressLine1) : ShipmentInterface
    {
        return $this->setParameter('addressLine1', $addressLine1);
    }

    /**
     * Get the recipient's address line 1 portion of their address.
     *
     * @return string
     */
    public function getAddressLine1() : string
    {
        return $this->getParameter('addressLine1');
    }

    /**
     * Set the recipient's address line 2 portion of their address.
     *
     * @param string $addressLine2
     *
     * @return ShipmentInterface
     */
    public function setAddressLine2(string $addressLine2) : ShipmentInterface
    {
        return $this->setParameter('addressLine2', $addressLine2);
    }

    /**
     * Get the recipient's address line 2 portion of their address.
     *
     * @return string|null
     */
    public function getAddressLine2()
    {
        return $this->getParameter('addressLine2');
    }

    /**
     * Set the recipient's address line 3 portion of their address.
     *
     * @param string $addressLine3
     *
     * @return ShipmentInterface
     */
    public function setAddressLine3(string $addressLine3) : ShipmentInterface
    {
        return $this->setParameter('addressLine3', $addressLine3);
    }

    /**
     * Get the recipient's address line 3 portion of their address.
     *
     * @return string|null
     */
    public function getAddressLine3()
    {
        return $this->getParameter('addressLine3');
    }

    /**
     * Set the recipient's post town portion of their address.
     *
     * @param string $postTown
     *
     * @return ShipmentInterface
     */
    public function setPostTown(string $postTown) : ShipmentInterface
    {
        return $this->setParameter('postTown', $postTown);
    }

    /**
     * Get the recipient's post town portion of their address.
     *
     * @return string
     */
    public function getPostTown() : string
    {
        return $this->getParameter('postTown');
    }

    /**
     * Set the recipient's postcode portion of their address.
     *
     * @param string $postcode
     *
     * @return ShipmentInterface
     */
    public function setPostcode(string $postcode) : ShipmentInterface
    {
        return $this->setParameter('postcode', $postcode);
    }

    /**
     * Get the recipient's postcode portion of their address.
     *
     * @return string
     */
    public function getPostcode() : string
    {
        return $this->getParameter('postcode');
    }

    /**
     * Set the recipient's country code portion of their address.
     *
     * @param string $countryCode
     *
     * @return ShipmentInterface
     */
    public function setCountry(string $countryCode) : ShipmentInterface
    {
        return $this->setParameter('country', $countryCode);
    }

    /**
     * Get the recipient's country code portion of their address.
     *
     * @return string|null
     */
    public function getCountry()
    {
        return $this->getParameter('country');
    }

    /**
     * Get the service enhancements.
     *
     * @return array|null
     */
    public function getServiceEnhancements()
    {
        return $this->getParameter('serviceEnhancements');
    }

    /**
     * Set the service enhancements.
     *
     * @param array $serviceEnhancements
     *
     * @return ShipmentInterface
     */
    public function setServiceEnhancements(array $serviceEnhancements) : ShipmentInterface
    {
        return $this->setParameter('serviceEnhancements', $serviceEnhancements);
    }

    /**
     * Update the shipment.
     *
     * @param $parameters
     *
     * @return ShipmentResponse
     */
    public function update(array $parameters)
    {
        Helper::initialise($this, $parameters);

        $this->notify('updated', $this);

        return new ShipmentResponse(['shipment' => $this]);
    }
}
