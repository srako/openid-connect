<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Http;

use JsonException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Srako\OpenIDConnect\Exception\HttpException;
use Srako\OpenIDConnect\Exception\RuntimeException;
use Srako\OpenIDConnect\Util\JWT;

final class HttpClient implements ClientInterface, RequestFactoryInterface, UriFactoryInterface, StreamFactoryInterface
{
    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        ClientInterface         $httpClient,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface     $uriFactory,
        StreamFactoryInterface  $streamFactory
    )
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->streamFactory = $streamFactory;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $response = $this->httpClient->sendRequest($request);
        $statusCode = $response->getStatusCode();

        if ($statusCode < 200 || $statusCode >= 400) {
            throw new HttpException($request, $response);
        }

        return $response;
    }

    /**
     * @param UriInterface|string $uri
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return $this->requestFactory->createRequest($method, $uri);
    }

    public function createUri(string $uri = ''): UriInterface
    {
        return $this->uriFactory->createUri($uri);
    }

    public function createStream(string $content = ''): StreamInterface
    {
        return $this->streamFactory->createStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return $this->streamFactory->createStreamFromFile($filename, $mode);
    }

    /**
     * @param resource $resource
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return $this->streamFactory->createStreamFromResource($resource);
    }

    /**
     * @param mixed[] $params
     */
    public function buildQueryString(array $params): string
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * @return mixed[]
     */
    public function parseResponse(ResponseInterface $response): array
    {
        try {
            return JWT::jsonToArray((string)$response->getBody());
        } catch (JsonException $e) {
            throw new RuntimeException('Unable to parse response: ' . $e->getMessage(), 0, $e);
        }
    }
}
