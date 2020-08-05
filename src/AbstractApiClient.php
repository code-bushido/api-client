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

use Bushido\ApiClient\Exceptions\BadResponseException;
use Bushido\ApiClient\Exceptions\ErrorResponseException;
use Bushido\ApiClient\Exceptions\TransportException;
use Bushido\ApiClient\Exceptions\WrongResponseException;
use Bushido\Foundation\Helpers\PsrLoggerTrait;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractApiClient
{
    use PsrLoggerTrait;

    private $client;
    private $logger;
    private $headers;

    public function __construct(array $config = [], LoggerInterface $logger = null, array $headers = [])
    {
        $this->setClient(new Client($config));
        $this->logger = $logger;
        $this->headers = $headers;
    }

    public static function make(string $baseUrl): self
    {
        return new static(['base_uri' => $baseUrl]);
    }

    /**
     * @param string $uri
     * @param array $query
     * @param array $headers
     * @return array
     * @throws BadResponseException For 5xx and unhandled 4xx
     * @throws ErrorResponseException For handled 4xx
     * @throws TransportException For errors on transport layer
     * @throws WrongResponseException Response in unexpected format or structure
     */
    public function get(string $uri, array $query = [], array $headers = [])
    {
        return $this->send((new Request('GET', $uri)), [], $query, $headers);
    }

    /**
     * @param string $uri
     * @param array $body
     * @param array $query
     * @param array $headers
     * @return array
     * @throws BadResponseException For 5xx and unhandled 4xx
     * @throws ErrorResponseException For handled 4xx
     * @throws TransportException For errors on transport layer
     * @throws WrongResponseException Response in unexpected format or structure
     */
    public function post(string $uri, array $body = [], array $query = [], array $headers = [])
    {
        return $this->send((new Request('POST', $uri)), $body, $query, $headers);
    }

    /**
     * @param string $uri
     * @param array $body
     * @param array $query
     * @param array $headers
     * @return array
     * @throws BadResponseException For 5xx and unhandled 4xx
     * @throws ErrorResponseException For handled 4xx
     * @throws TransportException For errors on transport layer
     * @throws WrongResponseException Response in unexpected format or structure
     */
    public function put(string $uri, array $body = [], array $query = [], array $headers = [])
    {
        return $this->send((new Request('PUT', $uri)), $body, $query, $headers);
    }

    /**
     * @param string $uri
     * @param array $body
     * @param array $query
     * @param array $headers
     * @return array
     * @throws BadResponseException For 5xx and unhandled 4xx
     * @throws ErrorResponseException For handled 4xx
     * @throws TransportException For errors on transport layer
     * @throws WrongResponseException Response in unexpected format or structure
     */
    public function patch(string $uri, array $body = [], array $query = [], array $headers = [])
    {
        return $this->send((new Request('PATCH', $uri)), $body, $query, $headers);
    }

    /**
     * @param string $uri
     * @param array $query
     * @param array $headers
     * @return array
     * @throws BadResponseException For 5xx and unhandled 4xx
     * @throws ErrorResponseException For handled 4xx
     * @throws TransportException For errors on transport layer
     * @throws WrongResponseException Response in unexpected format or structure
     */
    public function delete(string $uri, array $query = [], array $headers = [])
    {
        return $this->send((new Request('DELETE', $uri)), [], $query, $headers);
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    protected function setClient(ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    protected function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param RequestInterface $request
     * @param array $body
     * @param array $query
     * @param array $headers
     * @return array
     * @throws BadResponseException
     * @throws TransportException
     * @throws WrongResponseException
     * @throws ErrorResponseException
     */
    private function send(RequestInterface $request, array $body = [], array $query = [], array $headers = [])
    {
        $options = $this->processRequestBody($body);
        $this->processQuery($query, $options);
        $this->processHeaders($headers, $options);

        try {
            $response = $this->getClient()->send($request, $options);

            return $this->processResponse($response, $request);
        } catch (GuzzleException $e) {
            $this->handleException($e, $request);
        }
    }

    /**
     * @param GuzzleException $e
     * @param RequestInterface $request
     * @throws BadResponseException
     * @throws ErrorResponseException
     * @throws TransportException
     */
    protected function handleException(GuzzleException $e, RequestInterface $request)
    {
        if ($e instanceof Exception\ClientException) { // 4xx
            $this->processErrorResponse($e->getResponse(), $request);
            $this->logError(
                'Api unhandled [' . $e->getResponse()->getStatusCode() . '] Error Response from [' . $request->getUri() . ']',
                $this->formatBadResponseException($e)
            );

            throw new BadResponseException($e->getMessage(), $e->getCode(), $e);
        } elseif ($e instanceof Exception\BadResponseException) { // 4xx & 5xx
            $this->logError(
                'Api Bad Response from [' . $request->getUri() . '] Failed[' . $e->getResponse()->getStatusCode() . ']',
                $this->formatBadResponseException($e)
            );

            throw new BadResponseException($e->getMessage(), $e->getCode(), $e);
        } elseif ($e instanceof Exception\RequestException) {
            $this->logError(
                'Api problem with request to [' . $request->getUri() . ']',
                $this->formatRequestException($e)
            );
        }

        throw new TransportException($e->getMessage(), $e->getCode(), $e);
    }

    private function processQuery(array $query, array &$options): void
    {
        if (count($query) > 0) {
            $options['query'] = $query;
        }
    }

    private function processHeaders(array $headers, array &$options): void
    {
        $headers = array_merge($this->headers, $headers);

        $headers['Content-Length'] = 0; // Issue https://github.com/guzzle/guzzle/issues/1645

        if (count($headers) > 0) {
            $options['headers'] = $headers;
        }
    }

    private function formatBadResponseException(Exception\BadResponseException $e): array
    {
        return [
            'message' => $e->getMessage(),
            'request' => [
                'headers'   => $e->getRequest()->getHeaders(),
                'body'      => $e->getRequest()->getBody()->getContents(),
                'method'    => $e->getRequest()->getMethod(),
                'uri'       => $e->getRequest()->getUri(),
            ],
            'response' => [
                'body'      => ($e->getResponse())?$e->getResponse()->getBody()->getContents():'[EMPTY]',
                'headers'   => ($e->getResponse())?$e->getResponse()->getHeaders():'[EMPTY]',
            ],
        ];
    }

    private function formatRequestException(Exception\RequestException $e): array
    {
        return [
            'message' => $e->getMessage(),
            'request' => [
                'headers'   => $e->getRequest()->getHeaders(),
                'body'      => $e->getRequest()->getBody()->getContents(),
                'method'    => $e->getRequest()->getMethod(),
                'uri'       => $e->getRequest()->getUri(),
            ],
        ];
    }

    abstract protected function processRequestBody(array $body): array;

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @return array
     * @throws WrongResponseException
     */
    abstract protected function processResponse(ResponseInterface $response, RequestInterface $request): array;

    /**
     * @param ResponseInterface $response
     * @param RequestInterface $request
     * @throws ErrorResponseException
     */
    abstract protected function processErrorResponse(ResponseInterface $response, RequestInterface $request): void;
}
