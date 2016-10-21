<?php
/**
 * Base request class to be extended by all request classes
 */

namespace UniCarrier\Common;

/**
 * Class Request
 *
 * @package UniCarrier\Common
 */
abstract class Request implements RequestInterface
{
    use CarrierTrait;

    /**
     * The communicator to be used to make this request.
     *
     * @var CommunicatorInterface
     */
    protected $communicator;

    /**
     * Request constructor.
     *
     * @param CommunicatorInterface $communicator
     * @param array                 $parameters
     */
    public function __construct(CommunicatorInterface $communicator, array $parameters = [])
    {
        $this->communicator = $communicator;
        $this->initialise($parameters);
    }

    /**
     * Get the default parameters for a request.
     *
     * @return array
     */
    public function getDefaultParameters() : array
    {
        return [
            'transactionId' => null,
            'command' => null,
        ];
    }

    /**
     * Set the transaction id for this request.
     *
     * @param $transactionId
     *
     * @return RequestInterface
     */
    public function setTransactionId($transactionId) : RequestInterface
    {
        return $this->setParameter('transactionId', $transactionId);
    }

    /**
     * Get the transaction id for this request.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    /**
     * Set the command for this request.
     *
     * @return string|null
     */
    public function getCommand()
    {
        return $this->getParameter('command');
    }

    /**
     * Get the command for this request.
     *
     * @param $command
     *
     * @return RequestInterface
     */
    public function setCommand($command) : RequestInterface
    {
        return $this->setParameter('command', $command);
    }

    /**
     * Set the response for this request.
     *
     * @param ResponseInterface $response
     *
     * @return RequestInterface
     */
    public function setResponse(ResponseInterface $response) : RequestInterface
    {
        return $this->setParameter('response', $response);
    }

    /**
     * Get the response for this request.
     *
     * @return ResponseInterface
     */
    public function getResponse() : ResponseInterface
    {
        return $this->getParameter('response');
    }

    /**
     * Construct the request in the format required by the carrier and send it to them.
     *
     * @return ResponseInterface
     */
    public function send() : ResponseInterface
    {
        $data = $this->getData();

        return $this->sendData($data);
    }

    /**
     * Send the data constructed for this request.
     *
     * @param mixed $data
     *
     * @return ResponseInterface
     */
    abstract public function sendData($data) : ResponseInterface;

    /**
     * Construct the data in the format required by the carrier for this request.
     *
     * @return mixed
     */
    abstract public function getData();

    /**
     * Get the communicator for this request.
     *
     * @return CommunicatorInterface
     */
    public function getCommunicator() : CommunicatorInterface
    {
        return $this->communicator;
    }
}
