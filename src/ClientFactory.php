<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect;

use Psr\SimpleCache\CacheInterface;
use Srako\OpenIDConnect\Discovery\DiscovererFactory;
use Srako\OpenIDConnect\Http\HttpClient;
use Srako\OpenIDConnect\Http\HttpClientFactory;

final class ClientFactory
{
    public static function create(
        string $issuerUrl,
        ClientMetadata $clientMetadata,
        ?HttpClient $httpClient = null,
        ?CacheInterface $cache = null
    ): Client {
        $httpClient ??= HttpClientFactory::create();

        $discoverer = DiscovererFactory::create($httpClient, $cache);

        $providerMetadata = $discoverer->discover($issuerUrl);
        $config = new Config($providerMetadata, $clientMetadata);
        return new Client($config, $httpClient);
    }
}
