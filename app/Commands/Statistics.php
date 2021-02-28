<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\Statistics;
use Exception;
use RuntimeException;
use NanoFramework\Db;
use App\Services\Api;
use App\Services\App;
use NanoFramework\Console;
use NanoFramework\Http;
use NanoFramework\Cache;


Console::line('');
Console::success('************************************************');
Console::indicator('Bootstrap');
Console::line('Getting the Supermetrics Api posts statistics...');

// Create new app instance
$App = new App(
    api: new Api(
    http: Http::class,
    config: config()
),
    db: new Db(
    config: config()
),
    cache: Cache::class,
    config: config(),
    statistics: new Statistics
);

Console::indicator('App start');

try {

    // Generate statistics aggregations
    if ($App->getPostsFromApiAndSaveToBase()) {

        foreach ($App->getPostsFromBase() as $post) {
            $App->pushPostToStat($post);
        }
        $statResult = $App->getStatisticsByRow();

    } else {

        throw new RuntimeException('Error preparing posts data.');
    }

    // Print results
    Console::success('Total posts count');
    Console::line(json_encode($statResult['totalPostsCount'], JSON_THROW_ON_ERROR));

    Console::success('a. - Average character length of posts per month. "year-month":avg_post_length');
    Console::line(json_encode($statResult['avgPostLengthByMonth'], JSON_THROW_ON_ERROR));

    Console::success('b. - Longest post by character length per month. "year-month":post_id');
    Console::line(json_encode($statResult['longestPostLengthByMonth'], JSON_THROW_ON_ERROR));

    Console::success('c. - Total posts split by week number. "year-week":posts_count');
    Console::line(json_encode($statResult['totalPostCountByWeek'], JSON_THROW_ON_ERROR));

    Console::success('d. - Average number of posts per user per month. "user":posts_count');
    Console::line(json_encode($statResult['avgPostCountPerUserByMonth'], JSON_THROW_ON_ERROR));

} catch (Exception $e) {

    Console::error($e->getMessage());
}

// Final information
Console::indicator('App finish');
