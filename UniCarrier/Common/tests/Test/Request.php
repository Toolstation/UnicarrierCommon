<?php
/**
 * Test Request class
 */

namespace UniCarrier\Test;

use UniCarrier\Common\ResponseInterface;
use Mockery;

/**
 * Class Request
 *
 * @package UniCarrier\Test
 */
class Request extends \UniCarrier\Common\Request
{

    public function sendData($data) : ResponseInterface
    {
        return Mockery::mock('UniCarrier\Common\ResponseInterface');
    }

    public function getData()
    {
        return [];
    }
}
