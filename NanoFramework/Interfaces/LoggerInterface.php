<?php
declare(strict_types=1);

namespace NanoFramework\Interfaces;

/**
 * Interface LoggerInterface
 *
 * @package NanoFramework\Interfaces
 */
interface LoggerInterface
{
    /**
     * Put info line to log.
     *
     * @param string $data
     * @return bool
     */
    public function info(string $data): bool;

    /**
     * Put error line to log.
     *
     * @param string $data
     * @return bool
     */
    public function error(string $data): bool;
}
