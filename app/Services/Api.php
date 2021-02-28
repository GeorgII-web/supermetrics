<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ApiInterface;
use Exception;
use Generator;
use RuntimeException;
use SplFixedArray;

/**
 * Class Api
 *
 * @package app
 */
class Api implements ApiInterface
{
    public function __construct(protected $http, protected object $config)
    {
    }

    /**
     * Reformat responses array. Merge all separate responses to the one array.
     *
     * @param SplFixedArray $responses Array of json strings
     * @return array
     */
    protected function formatResponsesToPosts(SplFixedArray $responses): array
    {
        $res = [];

        foreach ($responses as $response) {
            if ($response) {
                try {

                    $posts = json_decode($response, true, 512, JSON_THROW_ON_ERROR)['data']['posts'];

                    foreach ($posts as $post) {
                        $res[] = $post;
                    }

                } catch (Exception) {

                    throw new RuntimeException('Error decoding json string from API response.');
                }
            }
        }

        return $res;
    }

    /**
     * Generate urls array to an Api posts endpoint by chunks.
     *
     * @param int    $pageCount Total pages count
     * @param string $token Token
     * @param int    $chunkSize Size of the chunk
     * @return array
     */
    protected function getPostsUrlsByChunks(int $pageCount, string $token, int $chunkSize = 0): array
    {
        $urls = [];
        $chunk = 0;

        for ($i = 0; $i < $pageCount; $i++) {

            if ($i % $chunkSize === 0) {
                $chunk++; // next chunk
            }

            $urls[$chunk][] = $this->config->api->domain .
                $this->config->api->posts .
                '?' .
                http_build_query([
                    'sl_token' => $token,
                    'page' => $i,
                ]);
        }

        return $urls;
    }

    /**
     * Get new token from register endpoint.
     *
     * @return string
     * @throws RuntimeException
     */
    public function getToken(): string
    {
        try {

            return json_decode(
                $this->http::post(
                    $this->config->api->domain . $this->config->api->register,
                    (array)$this->config->user
                ),
                true,
                512,
                JSON_THROW_ON_ERROR
            )['data']['sl_token'];

        } catch (Exception) {

            throw new RuntimeException('Error get token from API.');
        }
    }

    /**
     * Send multiple requests by chunks.
     * Example: send 2 chunks by 5 parallel requests.
     *
     * @param string $token
     * @return Generator
     */
    public function getPostsIterator(string $token): Generator
    {
        $chunkSize = $this->config->api->request_chunk_size;

        $urlsChunks = $this->getPostsUrlsByChunks($this->config->api->posts_pages, $token, $chunkSize);

        foreach ($urlsChunks as $urlsChunk) {

            yield $this->formatResponsesToPosts(
                $this->http::get($urlsChunk)
            );

        }
    }
}
