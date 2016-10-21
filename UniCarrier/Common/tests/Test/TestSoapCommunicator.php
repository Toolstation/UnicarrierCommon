<?php
/**
 * Class for testing SoapCommunicator class
 */

namespace UniCarrier\Test;

use UniCarrier\Common\SoapCommunicator;

class TestSoapCommunicator extends SoapCommunicator
{

    public function getWsdl() : string
    {
        return 'http://www.webservicex.com/globalweather.asmx?wsdl';
    }
}
