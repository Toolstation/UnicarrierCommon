<?php
/**
 * The interface to be implemented by all Parcel classes.
 */

namespace UniCarrier\Common;

/**
 * Interface ParcelInterface
 *
 * @package UniCarrier\Common
 */
interface ParcelInterface
{
    /**
     * Parcel constructor.
     *
     * @param string $weightUnits
     * @param int    $weightValue
     */
    public function __construct(string $weightUnits, int $weightValue);

    /**
     * Get the weight units for this parcel.
     *
     * @return string
     */
    public function getWeightUnits(): string;

    /**
     * Set the weight units for this parcel.
     *
     * @param string $weightUnits
     */
    public function setWeightUnits(string $weightUnits);

    /**
     * Get the weight value for this parcel.
     *
     * @return int
     */
    public function getWeightValue(): int;

    /**
     * Set the weight value for this parcel.
     *
     * @param int $weightValue
     */
    public function setWeightValue(int $weightValue);

    /**
     * Get the ID for this parcel.
     *
     * @return string
     */
    public function getId();

    /**
     * Set the ID for this parcel.
     *
     * @param string $id
     */
    public function setId(string $id);

    /**
     * Get the label for this parcel.
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Set the label for this parcel.
     *
     * @param mixed $label
     */
    public function setLabel(string $label);

    /**
     * Get the status.
     *
     * @return string|null
     */
    public function getStatus();

    /**
     * Set the status.
     *
     * @param string $status
     */
    public function setStatus(string $status);
}
