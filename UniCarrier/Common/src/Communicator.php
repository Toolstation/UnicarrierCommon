<?php
/**
 * Base Communicator class to be extended by all carriers.
 */

namespace UniCarrier\Common;

use Illuminate\Support\Collection;

/**
 * Class Communicator
 *
 * @package UniCarrier\Common
 */
abstract class Communicator implements CommunicatorInterface
{
    use CarrierTrait, ObservableTrait;

    /**
     * The parameters for this communicator.
     *
     * @var Collection
     */
    protected $parameters;

    /**
     * Communicator constructor.
     *
     * @param array $parameters
     * @throws \Exception Exception thrown if any default parameters are not set.
     */
    public function __construct(array $parameters = [])
    {
        $this->initialise($parameters);
        $missingDefaults = Helper::checkRequiredParametersSet(
            $this,
            array_keys($this->getDefaultParameters())
        );

        if (count($missingDefaults) > 0) {
            throw new \Exception("Missing default paramters :\n" . implode("\n", $missingDefaults));
        }
    }

    /**
     * Get the default parameters for a communicator.
     *
     * @return array
     */
    public function getDefaultParameters() : array
    {
        return [
            'testMode' => false,
            'testEndPoint' => null,
            'liveEndPoint' => null,
            'user' => null,
            'password' => null,
        ];
    }

    /**
     * Set the test mode.
     *
     * @param bool $testMode
     *
     * @return CommunicatorInterface
     */
    public function setTestMode(bool $testMode) : CommunicatorInterface
    {
        return $this->setParameter('testMode', $testMode);
    }

    /**
     * Get the test mode.
     *
     * @return bool
     */
    public function getTestMode() : bool
    {
        return $this->getParameter('testMode');
    }

    /**
     * Set the test end point.
     *
     * @param $testEndPoint
     *
     * @return CommunicatorInterface
     */
    public function setTestEndPoint($testEndPoint) : CommunicatorInterface
    {
        return $this->setParameter('testEndPoint', $testEndPoint);
    }

    /**
     * Get the test end point.
     *
     * @return string|null
     */
    public function getTestEndPoint()
    {
        return $this->getParameter('testEndPoint');
    }

    /**
     * Set the live end point.
     *
     * @param $liveEndPoint
     *
     * @return CommunicatorInterface
     */
    public function setLiveEndPoint($liveEndPoint) : CommunicatorInterface
    {
        return $this->setParameter('liveEndPoint', $liveEndPoint);
    }

    /**
     * Get the live endpoint.
     *
     * @return string|null
     */
    public function getLiveEndPoint()
    {
        return $this->getParameter('liveEndPoint');
    }

    /**
     * Get the endpoint based on the test mode.
     *
     * @return string|null
     */
    public function getEndPoint()
    {
        if ($this->getTestMode()) {
            return $this->getTestEndPoint();
        }

        return $this->getLiveEndPoint();
    }

    /**
     * Set the user.
     *
     * @param string $user
     *
     * @return CommunicatorInterface
     */
    public function setUser(string $user) : CommunicatorInterface
    {
        return $this->setParameter('user', $user);
    }

    /**
     * Get the user.
     *
     * @return string|null
     */
    public function getUser()
    {
        return $this->getParameter('user');
    }

    /**
     * Set the password.
     *
     * @param string $password
     *
     * @return CommunicatorInterface
     */
    public function setPassword(string $password) : CommunicatorInterface
    {
        return $this->setParameter('password', $password);
    }

    /**
     * Get the password.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * Set the carrier for this communicator.
     *
     * @param CarrierInterface $carrier
     *
     * @return CommunicatorInterface
     */
    public function setCarrier(CarrierInterface $carrier) : CommunicatorInterface
    {
        return $this->setParameter('carrier', $carrier);
    }

    /**
     * Get the carrier for this communicator.
     *
     * @return CarrierInterface
     */
    public function getCarrier() : CarrierInterface
    {
        return $this->getParameter('carrier');
    }

    /**
     * Send the data to the carrier provider.
     *
     * @param $data
     *
     * @return mixed
     */
    abstract public function sendData($data);
}
