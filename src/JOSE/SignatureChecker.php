<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Firebase\JWT\JWT;
use Srako\OpenIDConnect\Exception\AuthorizationException;
use Srako\OpenIDConnect\ProviderMetadata;
use stdClass;

final class SignatureChecker implements SignatureCheckerInterface
{
    private ProviderMetadata $providerMetadata;

    public function __construct(ProviderMetadata $providerMetadata)
    {
        $this->providerMetadata = $providerMetadata;
    }

    public function check(string $token): stdClass
    {
        $jwkSet = $this->providerMetadata->jwks();
        if (!$jwkSet) {
            throw new AuthorizationException('Cannot check token signature without JWKs.');
        }
        return JWT::decode($token, $jwkSet);
    }

}
