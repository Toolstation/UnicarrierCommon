<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 19/07/2016
 * Time: 16:59
 */

namespace UniCarrier\Test;

class Response extends \UniCarrier\Common\Response
{
    public function isSuccessful() : bool
    {
        return true;
    }
}
