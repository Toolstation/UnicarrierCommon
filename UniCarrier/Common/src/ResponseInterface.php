<?php
/**
 * Response Interface to be implemented by all response classes
 */

namespace UniCarrier\Common;

use Carbon\Carbon;

/**
 * Interface ResponseInterface
 *
 * @package UniCarrier\Common
 */
interface ResponseInterface
{
    /**
     * Set the time of the response.
     *
     * @param Carbon $timestamp
     *
     * @return ResponseInterface
     */
    public function setTimestamp(Carbon $timestamp) : ResponseInterface;

    /**
     * Get the time of the response.
     *
     * @return Carbon
     */
    public function getTimestamp() : Carbon;

    /**
     * Set the transaction ID of the response.
     *
     * @param $transactionId
     *
     * @return ResponseInterface
     */
    public function setTransactionId($transactionId) : ResponseInterface;

    /**
     * Get the transaction ID of the response.
     *
     * @return string|null
     */
    public function getTransactionId();

    /**
     * Return true if the response is sucessful.
     *
     * @return bool
     */
    public function isSuccessful() : bool;
}
