<?php
/**
 * Interface to be implemented by all observer classes
 */

namespace UniCarrier\Common;

/**
 * Interface ObserverInterface
 *
 * @package UniCarrier\Common
 */
interface ObserverInterface
{
    /**
     * Update using the action and data supplied.
     *
     * @param $action
     * @param $data
     *
     * @return void
     */
    public function update($action, $data);
}
