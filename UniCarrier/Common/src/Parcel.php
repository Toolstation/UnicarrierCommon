<?php
/**
 * A Parcel
 */

namespace UniCarrier\Common;

/**
 * Class Parcel
 *
 * @package UniCarrier\Common
 */
abstract class Parcel implements ParcelInterface
{
    /**
     * The weight units for this parcel.
     *
     * @var string
     */
    private $weightUnits;

    /**
     * The weight value for this parcel.
     *
     * @var int
     */
    private $weightValue;

    /**
     * The ID of this parcel.
     *
     * @var string
     */
    private $id;

    /**
     * The label for this parcel.
     *
     * @var string
     */
    private $label;

    /**
     * The status of the parcel.
     *
     * @var string
     */
    private $status;

    /**
     * Parcel constructor.
     *
     * @param string $weightUnits
     * @param int    $weightValue
     */
    public function __construct(string $weightUnits, int $weightValue)
    {
        $this->weightUnits = $weightUnits;
        $this->weightValue = $weightValue;
    }

    /**
     * Get the weight units for this parcel.
     *
     * @return string
     */
    public function getWeightUnits(): string
    {
        return $this->weightUnits;
    }

    /**
     * Set the weight units for this parcel.
     *
     * @param string $weightUnits
     */
    public function setWeightUnits(string $weightUnits)
    {
        $this->weightUnits = $weightUnits;
    }

    /**
     * Get the weight value for this parcel.
     *
     * @return int
     */
    public function getWeightValue(): int
    {
        return $this->weightValue;
    }

    /**
     * Set the weight value for this parcel.
     *
     * @param int $weightValue
     */
    public function setWeightValue(int $weightValue)
    {
        $this->weightValue = $weightValue;
    }

    /**
     * Get the ID for this parcel.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the ID for this parcel.
     *
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * Get the label for this parcel.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label for this parcel.
     *
     * @param mixed $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * Get the status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the status.
     *
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }
}
