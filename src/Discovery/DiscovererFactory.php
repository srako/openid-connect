<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Discovery;

use Psr\SimpleCache\CacheInterface;
use Srako\OpenIDConnect\Http\HttpClient;
use Srako\OpenIDConnect\Http\HttpClientFactory;

final class DiscovererFactory
{
    public static function create(?HttpClient $httpClient = null, ?CacheInterface $cache = null): Discoverer
    {
        $httpClient ??= HttpClientFactory::create();
        $discoverer = new HttpDiscoverer($httpClient);

        if ($cache !== null) {
            $discoverer = new CachedDiscoverer($discoverer, $cache);
        }

        return $discoverer;
    }
}
