<?php
declare(strict_types=1);

namespace NanoFramework;

use DateTime;
use Exception;
use NanoFramework\Interfaces\CacheItemInterface;

/**
 * Class Cache
 *
 * @package NanoFramework
 */
class Cache implements CacheItemInterface
{
    /**
     * Put data to cache by key.
     *
     * @param string $key   Cache key
     * @param string $value Cache value
     * @param int    $time  Cache time
     * @return bool Successful put
     */
    static public function put(string $key, string $value, int $time): bool
    {
        try {
            return file_put_contents($key . '.cache', json_encode([
                    'value' => $value,
                    'time' => $time,
                ], JSON_THROW_ON_ERROR)) > 0;

        } catch (Exception) {

            return false;
        }
    }

    /**
     * Get data from cache by key.
     *
     * @param string $key Cache key
     * @return string|null Cache value or null if has error
     */
    static public function get(string $key): ?string
    {
        if (!file_exists($key . '.cache')) {

            return null;
        }

        try {

            $data = (object)json_decode(file_get_contents($key . '.cache'), true, 512, JSON_THROW_ON_ERROR);

        } catch (Exception) {

            return null;
        }

        // File create time + cache time > now time - return cached value
        if ((filectime($key . '.cache') + $data->time) > (new DateTime())->getTimestamp()) {

            return $data->value;
        }

        return null;
    }

    /**
     * Clear all cached values.
     *
     * @param string $path Path
     * @return bool
     * @throws Exception
     */
    static public function clear(string $path): bool
    {
        try {
            array_map('unlink', glob($path . '*.cache'));

            return true;

        } catch (Exception) {

            throw new Exception('Error clearing cache.');
        }
    }
}
