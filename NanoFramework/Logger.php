<?php
declare(strict_types=1);

namespace NanoFramework;

use DateTime;
use Exception;
use NanoFramework\Interfaces\LoggerInterface;

/**
 * Class Log
 *
 * @package NanoFramework
 */
class Logger implements LoggerInterface
{
    public function __construct(protected object $config)
    {
    }

    /**
     * Generate formatted line.
     *
     * @param string $data
     * @param string $type
     * @return string
     */
    protected function generateLine(string $data, string $type): string
    {
        return '[' . (new DateTime())->format('Y-m-d H:i:s') . '] '
            . $type
            . ': '
            . $data
            . PHP_EOL;
    }

    /**
     * Put line to log.
     *
     * @param string $data
     * @param string $type
     * @return bool
     */
    protected function log(string $data, string $type): bool
    {
        try {
            return file_put_contents(
                    $this->config->log->path . $this->config->log->file,
                    $this->generateLine($data, $type),
                    FILE_APPEND | LOCK_EX
                ) > 0;

        } catch (Exception) {

            return false;
        }
    }

    /**
     * Put info line to log.
     *
     * @param string $data
     * @return bool
     */
    public function info(string $data): bool
    {
        return $this->log($data, 'INFO');
    }

    /**
     * Put error line to log.
     *
     * @param string $data
     * @return bool
     */
    public function error(string $data): bool
    {
        return $this->log($data, 'ERROR');
    }

}
