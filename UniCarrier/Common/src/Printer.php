<?php
/**
 * Used to produce labels and manifests.
 */

namespace UniCarrier\Common;

/**
 * Class Printer
 *
 * @package UniCarrier\Common
 */
class Printer
{
    /**
     * The print command
     *
     * @var null|string
     */
    private $printCommand = 'lpr -P';

    /**
     * Printer constructor.
     *
     * @param null $printCommand
     */
    public function __construct($printCommand = null)
    {
        if ($printCommand !== null) {
            $this->printCommand = $printCommand;
        }
    }

    /**
     * Print the source to the printer.
     *
     * @param $source
     * @codeCoverageIgnore
     */
    public function print($source)
    {
        $lpr = proc_open($this->printCommand, [0 => ['pipe', 'r']], $pipes, '/tmp');

        if (is_resource($lpr)) {
            fwrite($pipes[0], $source, strlen($source));
            fclose($pipes[0]);
            proc_close($lpr);
        }
    }
}
