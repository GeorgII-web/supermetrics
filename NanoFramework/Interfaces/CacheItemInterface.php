<?php
declare(strict_types=1);

namespace NanoFramework\Interfaces;

/**
 * Interface CacheItemInterface
 *
 * @package NanoFramework\Interfaces
 */
interface CacheItemInterface
{
    /**
     * Put data to cache by key.
     *
     * @param string $key   Cache key
     * @param string $value Cache value
     * @param int    $time  Cache time
     * @return bool Successful put
     */
    static public function put(string $key, string $value, int $time): bool;

    /**
     * Get data from cache by key.
     *
     * @param string $key Cache key
     * @return string|null Cache value or null if has error
     */
    static public function get(string $key): ?string;

}
