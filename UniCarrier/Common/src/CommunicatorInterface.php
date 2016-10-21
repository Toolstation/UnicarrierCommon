<?php
/**
 * Interface to be implemented by each carrier as a communicator class
 */

namespace UniCarrier\Common;

/**
 * Interface CommunicatorInterface
 *
 * @package UniCarrier\Common
 */
interface CommunicatorInterface extends ObservableInterface
{
    /**
     * CommunicatorInterface constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters);

    /**
     * Get the default parameters for the communicator.
     *
     * @return array
     */
    public function getDefaultParameters() : array;

    /**
     * Get the user.
     *
     * @return string
     */
    public function getUser();

    /**
     * Set the carrier for this communicator.
     *
     * @param CarrierInterface $carrier
     *
     * @return CommunicatorInterface
     */
    public function setCarrier(CarrierInterface $carrier) : CommunicatorInterface;

    /**
     * Get the carrier for this communicator.
     *
     * @return CarrierInterface
     */
    public function getCarrier() : CarrierInterface;

    /**
     * Send data to the carrier provider.
     *
     * @param $data
     *
     * @return mixed
     */
    public function sendData($data);
}
