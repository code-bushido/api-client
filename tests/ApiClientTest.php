<?php declare(strict_types=1);
/*
 * This file is part of the Bushido\ApiClient package.
 *
 * (c) Wojciech Nowicki <wnowicki@me.com>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace BushidoTests\ApiClient;

use Bushido\ApiClient\ApiClient;
use PHPUnit\Framework\TestCase;

class ApiClientTest extends TestCase
{
    public function testMake()
    {
        $this->assertInstanceOf(ApiClient::class, ApiClient::make('http://httpbin.org/'));
    }

    public function testGet()
    {
        $api = ApiClient::make('http://httpbin.org/');

        $this->assertIsArray($api->get('get'));
    }

    public function testQueryIsWorking()
    {
        $api = ApiClient::make('http://httpbin.org/');

        $response = $api->get('get', ['a' => 'b', 'c' => 'd'], ['test' => 123]);

        $this->assertArrayHasKey('args', $response);

        $this->assertCount(2, $response['args']);
    }

    public function testPost()
    {
        $api = ApiClient::make('http://httpbin.org/');

        $this->assertIsArray($api->post('post'));
    }

    public function testDelete()
    {
        $api = ApiClient::make('http://httpbin.org/');

        $this->assertIsArray($api->delete('delete'));
    }

    public function testPut()
    {
        $api = ApiClient::make('http://httpbin.org/');

        $this->assertIsArray($api->put('put'));
    }

    public function testPatch()
    {
        $api = ApiClient::make('http://httpbin.org/');

        $this->assertIsArray($api->patch('patch'));
    }

    public function testNoContentResponse()
    {
        $api = ApiClient::make('http://httpbin.org/');

        $response = $api->post('status/204');

        $this->assertIsArray($response);
        $this->assertCount(0, $response);
    }
}
