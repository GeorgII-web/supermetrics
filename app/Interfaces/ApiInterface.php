<?php
declare(strict_types=1);

namespace App\Interfaces;

use Generator;

/**
 * Interface ApiInterface
 *
 * @package App\Interfaces
 */
interface ApiInterface
{
    /**
     * Get new token from register endpoint.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Send multiple requests by chunks.
     * Example: send 2 chunks by 5 parallel requests.
     *
     * @param string $token
     * @return Generator
     */
    public function getPostsIterator(string $token): Generator;
}
