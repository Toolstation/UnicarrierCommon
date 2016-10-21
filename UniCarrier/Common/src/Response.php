<?php
/**
 * The base response class. This is to be extended by all responses.
 */

namespace UniCarrier\Common;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Response
 *
 * @package UniCarrier\Common
 */
abstract class Response implements ResponseInterface
{
    use CarrierTrait;

    /**
     * The parameters for this reqponse.
     *
     * @var Collection
     */
    protected $parameters;

    /**
     * The data in the response received from the carrier.
     *
     * @var mixed
     */
    protected $data;

    /**
     * Response constructor.
     *
     * @param mixed            $data
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->initialise(['timestamp' => new Carbon()]);
    }

    /**
     * Get the default parameters for a response.
     *
     * @return array
     */
    public function getDefaultParameters() : array
    {
        return [
            'carrierId' => null,
            'timestamp' => null,
            'transactionId' => null,
        ];
    }

    /**
     * Set the time of the response.
     *
     * @param Carbon $timestamp
     *
     * @return ResponseInterface
     */
    public function setTimestamp(Carbon $timestamp) : ResponseInterface
    {
        return $this->setParameter('timestamp', $timestamp);
    }

    /**
     * Get the time of the response.
     *
     * @return Carbon
     */
    public function getTimestamp() : Carbon
    {
        return $this->getParameter('timestamp');
    }

    /**
     * Set the transaction ID for the response.
     *
     * @param $transactionId
     *
     * @return ResponseInterface
     */
    public function setTransactionId($transactionId) : ResponseInterface
    {
        return $this->setParameter('transactionId', $transactionId);
    }

    /**
     * Get the transaction ID for the response.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    /**
     * Returns true if this is a successful response.
     *
     * @return bool
     */
    abstract public function isSuccessful() : bool;
}
