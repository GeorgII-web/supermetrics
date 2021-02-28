<?php
declare(strict_types=1);

/**
 * Global config file.
 *
 * @return object
 */
function config(): object
{
    return (object)[
        'api' => (object)[
            'domain' => 'https://api.supermetrics.com/',
            'register' => 'assignment/register',
            'posts' => 'assignment/posts',
            'posts_pages' => 10,
            'posts_per_page' => 100,
            'request_chunk_size' => 5,
        ],
        'user' => (object)[
            'client_id' => 'ju16a6m81mhid5ue1z3v2g0uh',
            'email' => 'your@email.address',
            'name' => 'Your Name',
        ],
        'cache' => (object)[
            'path' => 'data/cache/',
            'time' => 3600,
            'time_posts' => 60,
        ],
        'db' => (object)[
            'path' => 'data/base/',
        ],
    ];
}
