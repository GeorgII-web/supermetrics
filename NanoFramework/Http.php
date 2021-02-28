<?php
declare(strict_types=1);

namespace NanoFramework;

use NanoFramework\Interfaces\HttpInterface;
use SplFixedArray;
use Throwable;

/**
 * Class Http
 *
 * @package NanoFramework
 */
class Http implements HttpInterface
{
    /**
     * Get multiple content by urls.
     *
     * @param array|string $urls
     * @return SplFixedArray|null
     */
    public static function get(array|string $urls): ?SplFixedArray
    {
        $ch = [];

        $urlsArr = (array)(is_array($urls)) ? (array)$urls : [0 => (string)$urls];
        $responses = new SplFixedArray(count($urls));

        try {
            $mh = curl_multi_init();

            // Add handlers
            foreach ($urlsArr as $key => $value) {
                $ch[$key] = curl_init($value);
                curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($mh, $ch[$key]);
            }

            // Multi exec urls
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);

            // Get results of the multi exec
            foreach (array_keys($ch) as $key) {
                if (curl_getinfo($ch[$key], CURLINFO_HTTP_CODE) === 200) {
                    $responses[$key] = curl_multi_getcontent($ch[$key]);
                } else {
                    $responses[$key] = null;
                }

                curl_multi_remove_handle($mh, $ch[$key]);
            }

            curl_multi_close($mh);

        } catch (Throwable) {

            return null;
        }

        return $responses;
    }

    /**
     * @param string $url
     * @param array  $info
     * @return bool|string
     */
    public static function post(string $url, array $info): bool|string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $info);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;
    }
}
