<?php
/**
 * The methods needed for observable classes
 */

namespace UniCarrier\Common;

/**
 * Class ObservableTrait
 *
 * @package UniCarrier\Common
 */
trait ObservableTrait
{
    /**
     * An array of class instances observing this class.
     *
     * @var ObserverInterface[]
     */
    protected $observers = [];
    
    /**
     * Add an observer.
     *
     * @param ObserverInterface $observer
     *
     * @return mixed
     */
    public function register($observer)
    {
        $this->observers[] = $observer;

        return $this;
    }

    /**
     * Remove an observer.
     *
     * @param ObserverInterface $observer
     *
     * @return mixed
     */
    public function detach(ObserverInterface $observer)
    {
        $this->observers = array_filter($this->observers, function ($a) use ($observer) {
            return (!($a === $observer));
        });
    }

    /**
     * Notify all observers
     *
     * @param string $action
     * @param mixed  $data
     */
    public function notify(string $action, $data)
    {
        foreach ($this->observers as $observer) {
            $observer->update($action, $data);
        }
    }
}
