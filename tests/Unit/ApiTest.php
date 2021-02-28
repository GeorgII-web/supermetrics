<?php

namespace Test\Unit;

use App\Services\Api;
use NanoFramework\Http;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testApiToken()
    {
        $token = (new Api(
            http: Http::class,
            config: config()
        ))->getToken();

        $this->assertEquals('smslt_', substr($token, 0, 6));
    }

    public function testApiUrlsChunks()
    {
        $Api = (new Api(
            http: Http::class,
            config: config()
        ));

        $urls = $Api->getPostsIterator($Api->getToken());

        $res = [];
        foreach ($urls as $url) {
            $res[] = $url;
        }

        $this->assertEquals(2, count($res));
    }

}
