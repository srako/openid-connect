<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Token;

use Srako\OpenIDConnect\JOSE\ClaimsChecker;
use Srako\OpenIDConnect\JOSE\JOSEFactory;
use Srako\OpenIDConnect\JOSE\SignatureChecker;
use Srako\OpenIDConnect\ProviderMetadata;

final class TokenVerifierFactory
{
    public static function create(
        ProviderMetadata $providerMetadata,
        ?SignatureChecker $signatureChecker = null,
        ?ClaimsChecker $claimsChecker = null
    ): TokenVerifier {
        $signatureChecker ??= new SignatureChecker($providerMetadata, new JOSEFactory());
        $claimsChecker ??= new ClaimsChecker();

        return new TokenVerifier($signatureChecker, $claimsChecker);
    }
}
