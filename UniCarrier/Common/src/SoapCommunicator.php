<?php
/**
 * Communicator for SOAP interfaces.
 */

namespace UniCarrier\Common;

/**
 * Class SoapCommunicator
 *
 * @package UniCarrier\Common
 */
abstract class SoapCommunicator extends Communicator
{
    /**
     * Get the SOAP client.
     *
     * @return mixed|\SoapClient
     */
    public function getClient()
    {
        $soapClient = $this->getParameter('client');

        if ($soapClient === null) {
            $soapClient = new \SoapClient(
                $this->getWsdl(),
                $this->getClientOptions()
            );
        }

        $this->setParameter('client', $soapClient);

        return $soapClient;
    }

    /**
     * Get the options to be used for this SOAP client.
     *
     * @return array
     */
    public function getClientOptions() : array
    {
        return [];
    }

    /**
     * Get the wsdl to be used for this SOAP request.
     *
     * @return string
     */
    abstract public function getWsdl() : string;

    /**
     * Send the data using SOAP
     * @param mixed $data
     *
     * @return Response
     * @throws \Exception
     */
    public function sendData($data)
    {
        $soapClient = $this->getClient();

        if (is_object($soapClient)) {
            try {
                $response = $soapClient->__soapCall(
                    $data['soapFunction'],
                    $data['payload']
                );
            } catch (\Exception $e) {
                $response = $e;
            }

            return $response;
        } else {
            throw new \Exception('couldn\'t make the request.');
        }
    }
}
