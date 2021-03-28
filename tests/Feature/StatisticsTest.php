<?php

use App\Services\Api;
use App\Services\App;
use App\Services\Statistics;
use NanoFramework\Cache;
use NanoFramework\Db;
use NanoFramework\Http;


//Prepare statistics information
$App = new App(
    api: new Api(
    http: new Http,
    config: config()
),
    db: new Db(
    config: config()
),
    cache: new Cache,
    config: config(),
    statistics: new Statistics
);

$stat = [
    'totalPostsCount' => 0,
    'avgPostLengthByMonth' => [],
    'longestPostLengthByMonth' => [],
    'totalPostCountByWeek' => [],
    'avgPostCountPerUserByMonth' => [],
];

try {
    if ($App->getPostsFromApiAndSaveToBase()) {

        foreach ($App->getPostsFromBase() as $post) {
            $App->pushPostToStat($post);
        }

        $stat = $App->getStatisticsByRow();
    }
} catch (Exception) {
}

// Test statistics information
test('posts count', function () use ($stat) {
    expect($stat['totalPostsCount'])->toBe(1000);
});

test('longest post length by month', function () use ($stat) {
    expect(count($stat['longestPostLengthByMonth']))->toBeGreaterThan(1);
});

test('total post count by week', function () use ($stat) {
    expect(count($stat['totalPostCountByWeek']))->toBeGreaterThan(1);
});

test('avg post length by month', function () use ($stat) {
    expect(count($stat['avgPostLengthByMonth']))->toBeGreaterThan(1);
});

test('avg post count per user by month', function () use ($stat) {
    expect(count($stat['avgPostCountPerUserByMonth']))->toBeGreaterThan(1);
});
