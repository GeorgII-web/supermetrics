<?php

namespace Test\Feature;

use App\Services\Api;
use App\Services\App;
use App\Services\Statistics;
use NanoFramework\Cache;
use NanoFramework\Db;
use NanoFramework\Http;
use PHPUnit\Framework\TestCase;

class StatisticsTest extends TestCase
{
    protected $stat = [];

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

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

        if ($App->getPostsFromApiAndSaveToBase()) {

            foreach ($App->getPostsFromBase() as $post) {
                $App->pushPostToStat($post);
            }

            $this->stat = $App->getStatisticsByRow();
        }
    }

    public function testPostsCount()
    {
        $this->assertEquals(1000, $this->stat['totalPostsCount']);
    }

    public function testLongestPostLengthByMonth()
    {
        $this->assertEquals(6, count($this->stat['longestPostLengthByMonth']));
    }

    public function testTotalPostCountByWeek()
    {
        $this->assertEquals(26, count($this->stat['totalPostCountByWeek']));
    }

    public function testAvgPostLengthByMonth()
    {
        $this->assertEquals(6, count($this->stat['avgPostLengthByMonth']));
    }

    public function testAvgPostCountPerUserByMonth()
    {
        $this->assertEquals(20, count($this->stat['avgPostCountPerUserByMonth']));
    }
}
