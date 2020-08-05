<?php declare(strict_types=1);
/*
 * This file is part of the Bushido\ApiClient package.
 *
 * (c) Wojciech Nowicki <wnowicki@me.com>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Bushido\ApiClient;

use Bushido\ApiClient\Exceptions\ErrorResponseException;
use Bushido\ApiClient\Exceptions\WrongResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiClient extends AbstractApiClient
{
    protected function processRequestBody(array $body): array
    {
        return ['json' => $body];
    }

    protected function processResponse(ResponseInterface $response, RequestInterface $request): array
    {
        if ($response->getStatusCode() == 204) {
            return [];
        }

        $responseBody = json_decode($response->getBody()->getContents(), true);

        if (is_array($responseBody)) {
            return $responseBody;
        }

        throw new WrongResponseException('Response body was malformed JSON', $response->getStatusCode());
    }

    protected function processErrorResponse(ResponseInterface $response, RequestInterface $request): void
    {
        if (($responseBody = json_decode($response->getBody()->getContents(), true)) &&
            array_key_exists('message', $responseBody)
        ) {
            throw new ErrorResponseException($responseBody['message'], $response->getStatusCode());
        }
    }

}
