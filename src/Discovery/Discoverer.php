<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Discovery;

use Srako\OpenIDConnect\Exception\DiscoveryException;
use Srako\OpenIDConnect\ProviderMetadata;

interface Discoverer
{
    /**
     * @throws DiscoveryException
     */
    public function discover(string $issuerUrl): ProviderMetadata;
}
