<?php
declare(strict_types=1);

namespace NanoFramework\Interfaces;

use SplFixedArray;

/**
 * Interface HttpInterface
 *
 * @package NanoFramework\Interfaces
 */
interface HttpInterface
{
    /**
     * Get multiple content by urls.
     *
     * @param array|string $urls
     * @return SplFixedArray|null
     */
    public static function get(array|string $urls): ?SplFixedArray;

    /**
     * @param string $url
     * @param array  $info
     * @return bool|string
     */
    public static function post(string $url, array $info): bool|string;
}
