<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Token;

use Srako\OpenIDConnect\JOSE\Claims;
use Srako\OpenIDConnect\JOSE\ClaimsCheckerInterface;
use Srako\OpenIDConnect\JOSE\SignatureCheckerInterface;
use Srako\OpenIDConnect\Param\ClaimsChecks;

final class TokenVerifier implements TokenVerifierInterface
{
    private SignatureCheckerInterface $signatureChecker;
    private ClaimsCheckerInterface $claimsChecker;

    public function __construct(
        SignatureCheckerInterface $signatureChecker,
        ClaimsCheckerInterface    $claimsChecker
    )
    {
        $this->signatureChecker = $signatureChecker;
        $this->claimsChecker = $claimsChecker;
    }

    public function verify(string $token, ClaimsChecks $checks): void
    {
        $this->signatureChecker->check($token);
        $this->claimsChecker->check(Claims::fromToken($token), $checks);
    }
}
