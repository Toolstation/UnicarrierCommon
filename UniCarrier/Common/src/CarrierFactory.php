<?php
/**
 * Carrier factory
 */

namespace UniCarrier\Common;

/**
 * Class CarrierFactory
 *
 * @package UniCarrier\Common
 */
class CarrierFactory
{
    /**
     * Creates a carrier of the carrier type corresponding to the name.
     *
     * @param string                     $carrierName
     * @param array                      $parameters
     * @param CommunicatorInterface|null $communicator
     *
     * @return CarrierInterface
     */
    public static function createCarrier(
        string $carrierName,
        array $parameters = [],
        CommunicatorInterface $communicator = null
    ): CarrierInterface {
        $class = Helper::getCarrierClassName($carrierName);

        if (!class_exists($class)) {
            throw new \RuntimeException("Class '$class' not found");
        } //@codeCoverageIgnore

        return new $class($parameters, $communicator);
    } //@codeCoverageIgnore
}
