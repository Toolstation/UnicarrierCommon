<?php
/**
 * The request interface. Implemented by all request classes.
 */

namespace UniCarrier\Common;

/**
 * Interface RequestInterface
 *
 * @package UniCarrier\Common
 */
interface RequestInterface
{
    /**
     * Initialise request with parameters
     *
     * @param array $parameters
     */
    public function initialise(array $parameters = []);

    /**
     * Get all request parameters
     *
     * @return array
     */
    public function getParameters() : array;

    /**
     * Set the response to this request.
     *
     * @param ResponseInterface $response
     *
     * @return RequestInterface
     */
    public function setResponse(ResponseInterface $response) : RequestInterface;

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseInterface
     */
    public function getResponse() : ResponseInterface;

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send() : ResponseInterface;

    /**
     * Send the request with specified data
     *
     * @param  mixed             $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data) : ResponseInterface;
}
