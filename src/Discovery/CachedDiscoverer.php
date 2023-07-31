<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Discovery;

use Srako\OpenIDConnect\ProviderMetadata;
use Psr\SimpleCache\CacheInterface;

final class CachedDiscoverer implements Discoverer
{
    public const DEFAULT_TTL = 3600;

    private Discoverer $inner;
    private CacheInterface $cache;
    private int $ttl;

    public function __construct(Discoverer $inner, CacheInterface $cache, int $ttl = self::DEFAULT_TTL)
    {
        $this->inner = $inner;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    public function discover(string $issuerUrl): ProviderMetadata
    {
        $key = 'oidc_discoverer_' . base64_encode($issuerUrl);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $metadata = $this->inner->discover($issuerUrl);
        $this->cache->set($key, $metadata, $this->ttl);

        return $metadata;
    }
}
