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
    protected array $stat = [];

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

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
        $weeksCount = count($this->stat['totalPostCountByWeek']);
        $this->assertTrue($weeksCount === 27 || $weeksCount === 26);
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
