<?php
/**
 * Response sent on completion of a successful command
 */

namespace UniCarrier\Common;

/**
 * Class SuccessResponse
 *
 * @package UniCarrier\Common
 */
abstract class SuccessResponse extends Response
{
    /**
     * An array of warning messages.
     *
     * @var array
     */
    private $warnings;

    /**
     * ShipmentResponse constructor.
     *
     * @param array            $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->warnings = isset($data['warnings']) ? $data['warnings'] : [];
    }

    /**
     * Indicates that the action was successful.
     *
     * @return bool
     */
    public function isSuccessful() : bool
    {
        return true;
    }

    /**
     * Get the warnings.
     *
     * @return array|null
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Get the warnings as a string.
     *
     * @return string
     */
    public function getMessage() : string
    {
        return "Warnings:\n" . implode("\n", $this->warnings);
    }
}
