<?php
/**
 * Error response.
 */

namespace UniCarrier\Common;

/**
 * Class ErrorResponse
 *
 * @package UniCarrier\Common
 */
class ErrorResponse extends Response
{
    /**
     * Errors found
     *
     * @var array
     */
    private $errors = [];

    /**
     * Warnings found
     *
     * @var array|null
     */
    private $warnings = [];

    /**
     * ErrorResponse constructor.
     *
     * @param mixed $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->warnings = isset($data['warnings']) ? $data['warnings'] : [];
        $this->errors = $data['errors'];
    }

    /**
     * This is an error response.
     *
     * @return bool
     */
    public function isSuccessful() : bool
    {
        return false;
    }

    /**
     * Get the errors on this response.
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * Get any warnings on this response.
     *
     * @return array|null
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Get the errors and warnings as a string.
     *
     * @return string
     */
    public function getMessage() : string
    {
        $return = "Errors:\n" . implode("\n", $this->errors);
        if ($this->warnings !== null && !empty($this->warnings)) {
            $return .= "\nWarnings:\n" . implode("\n", $this->warnings);
        }

        return $return;
    }
}
