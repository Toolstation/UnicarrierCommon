<?php
/**
 * Common methods for Carrier classes
 */

namespace UniCarrier\Common;

use Illuminate\Support\Collection;

/**
 * Class CarrierTrait
 *
 * @package UniCarrier\Common
 */
trait CarrierTrait
{
    /**
     * The parameters for this carrier.
     *
     * @var Collection
     */
    protected $parameters;

    /**
     * Initialise the parameters using the array passed
     *
     * @param array $parameters
     *
     * @return $this
     */
    public function initialise(array $parameters = [])
    {
        // set default parameters
        $this->parameters =  new Collection();

        $defaults = $this->getDefaultParameters();
        foreach ($this->getDefaultParameters() as $key => $value) {
            if (is_array($value)) {
                $this->parameters->put($key, reset($value));
            } else {
                $this->parameters->put($key, $value);
            }
        }

        Helper::initialise($this, $parameters);

        return $this;
    }

    /**
     * Get an array of all of the parameters set for the carrier.
     *
     * @return array
     */
    public function getParameters() : array
    {
        return $this->parameters->all();
    }

    /**
     * Get the value of the supplied parameter name.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getParameter(string $key)
    {
        return $this->parameters->get($key);
    }

    /**
     * Set the parameter value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return mixed
     */
    protected function setParameter(string $key, $value)
    {
        $this->parameters->put($key, $value);

        return $this;
    }
}
