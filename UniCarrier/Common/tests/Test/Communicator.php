<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 19/07/2016
 * Time: 14:55
 */

namespace UniCarrier\Test;

use UniCarrier\Common\ResponseInterface;

class Communicator extends \UniCarrier\Common\Communicator
{

    public function getClient()
    {
        // TODO: Implement getClient() method.
    }

    public function sendData($data) : ResponseInterface
    {
        // TODO: Implement sendData() method.
    }

    public function createResponse($request, $response) : ResponseInterface
    {
        // TODO: Implement createResponse() method.
    }
}
