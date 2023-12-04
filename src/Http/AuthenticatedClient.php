<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Srako\OpenIDConnect\Client;
use Srako\OpenIDConnect\Token\Tokens;

final class AuthenticatedClient implements ClientInterface
{
    private Client $client;
    private HttpClient $httpClient;
    private Tokens $tokens;

    public function __construct(
        Client     $client,
        HttpClient $httpClient,
        Tokens     $tokens
    )
    {
        $this->client = $client;
        $this->httpClient = $httpClient;
        $this->tokens = $tokens;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->tokens = $this->client->refreshTokens($this->tokens);

        return $this->httpClient->sendRequest(
            $request->withHeader('Authorization', "Bearer {$this->tokens->accessToken()}")
                ->withHeader('Accept', 'application/json')
                ->withHeader('X-Client-Id', $this->client->getConfig()->clientMetadata()->id()),
        );
    }

    public function httpClient(): HttpClient
    {
        return $this->httpClient;
    }
    public function client(): Client
    {
        return $this->client;
    }

    public function tokens(): Tokens
    {
        return $this->tokens;
    }
}
