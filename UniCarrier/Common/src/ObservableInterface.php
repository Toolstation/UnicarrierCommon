<?php
/**
 * Observable Interface
 */

namespace UniCarrier\Common;

/**
 * Interface ObservableInterface
 *
 * @package UniCarrier\Common
 */
interface ObservableInterface
{
    /**
     * Add an observer.
     *
     * @param ObserverInterface $observer
     *
     * @return mixed
     */
    public function register($observer);

    /**
     * Remove an observer.
     *
     * @param ObserverInterface $observer
     *
     * @return mixed
     */
    public function detach(ObserverInterface $observer);

    /**
     * Notify all observers
     *
     * @param string $action
     * @param mixed  $data
     */
    public function notify(string $action, $data);
}
