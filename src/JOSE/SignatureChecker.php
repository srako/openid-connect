<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Srako\OpenIDConnect\Exception\AuthorizationException;
use Srako\OpenIDConnect\ProviderMetadata;
use Jose\Component\Signature\JWSLoader;

final class SignatureChecker implements SignatureCheckerInterface
{
    private ProviderMetadata $providerMetadata;
    private JOSEFactory $JOSEFactory;

    public function __construct(
        ProviderMetadata $providerMetadata,
        JOSEFactory      $JOSEFactory
    )
    {
        $this->providerMetadata = $providerMetadata;
        $this->JOSEFactory = $JOSEFactory;
    }

    public function check(string $token): void
    {
        $jwkSet = $this->providerMetadata->jwks();
        if (!$jwkSet) {
            throw new AuthorizationException('Cannot check token signature without JWKs.');
        }
        $this->createJWSLoader()->loadAndVerifyWithKeySet($token, $jwkSet, $signature);
    }

    private function createJWSLoader(): JWSLoader
    {
        return $this->JOSEFactory->createJWSLoader($this->providerMetadata);
    }
}
