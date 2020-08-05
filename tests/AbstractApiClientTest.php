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
use Bushido\ApiClient\Exceptions\BadResponseException;
use Bushido\ApiClient\Exceptions\TransportException;
use Bushido\ApiClient\Exceptions\WrongResponseException;
use PHPUnit\Framework\TestCase;

class AbstractApiClientTest extends TestCase
{
    public function testClientException()
    {
        $api = ApiClient::make('http://httpbin.org/status/418');

        $this->expectException(BadResponseException::class);

        $api->get('');
    }

    public function testWrongResponse()
    {
        $api = ApiClient::make('http://httpbin.org/xml');

        $this->expectException(WrongResponseException::class);

        $api->get('');
    }

    public function testBadRequest()
    {
        $api = ApiClient::make('htctp://httpbin.org/xml');

        $this->expectException(TransportException::class);

        $api->get('');
    }

    public function testBadResponse()
    {
        $api = ApiClient::make('http://httpbin.org/status/500');

        $this->expectException(BadResponseException::class);

        $api->get('');
    }
}
