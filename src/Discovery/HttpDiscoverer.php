<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Discovery;

use Srako\OpenIDConnect\Exception\DiscoveryException;
use Srako\OpenIDConnect\Exception\RuntimeException;
use Srako\OpenIDConnect\Http\HttpClient;
use Srako\OpenIDConnect\ProviderMetadata;
use Jose\Component\Core\JWKSet;
use Psr\Http\Client\ClientExceptionInterface;

final class HttpDiscoverer implements Discoverer
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @throws DiscoveryException
     */
    public function discover(string $issuerUrl): ProviderMetadata
    {
        $issuerUrl .= '/.well-known/openid-configuration';
        $configuration = $this->sendRequest($issuerUrl);
        $jwks = $this->sendRequest($configuration['jwks_uri']);

        return new ProviderMetadata($configuration, JWKSet::createFromKeyData($jwks));
    }

    /**
     * @return array<string, mixed>
     */
    private function sendRequest(string $issuerUrl): array
    {
        $request = $this->httpClient->createRequest('GET', $issuerUrl);

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new DiscoveryException($e->getMessage(), $e->getCode(), $e);
        }

        try {
            return $this->httpClient->parseResponse($response);
        } catch (RuntimeException $e) {
            throw new DiscoveryException('Unable to parse response from ' . $issuerUrl, $e->getCode(), $e);
        }
    }
}
