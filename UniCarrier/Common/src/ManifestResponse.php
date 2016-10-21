<?php
/**
 * Response sent on completion of a successful manifest command
 */

namespace UniCarrier\Common;

/**
 * Class ManifestResponse
 *
 * @package UniCarrier\Common
 */
class ManifestResponse extends SuccessResponse
{
    /**
     * The manifest.
     *
     * @var ManifestInterface
     */
    private $manifest;

    /**
     * ManifestResponse constructor.
     *
     * @param mixed            $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->manifest = $data['manifest'];
    }

    /**
     * Get the manifest.
     *
     * @return ManifestInterface
     */
    public function getManifest() : ManifestInterface
    {
        return $this->manifest;
    }
}
