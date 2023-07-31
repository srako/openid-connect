<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Http;

use Srako\OpenIDConnect\Client;
use Srako\OpenIDConnect\Token\Tokens;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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
            $request->withHeader('Authorization', "Bearer {$this->tokens->accessToken()}"),
        );
    }
}
