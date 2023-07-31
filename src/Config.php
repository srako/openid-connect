<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect;

final class Config
{
    private ProviderMetadata $providerMetadata;
    private ClientMetadata $clientMetadata;

    public function __construct(ProviderMetadata $providerMetadata, ClientMetadata $clientMetadata)
    {
        $this->providerMetadata = $providerMetadata;
        $this->clientMetadata = $clientMetadata;
    }

    public function providerMetadata(): ProviderMetadata
    {
        return $this->providerMetadata;
    }

    public function clientMetadata(): ClientMetadata
    {
        return $this->clientMetadata;
    }
}
