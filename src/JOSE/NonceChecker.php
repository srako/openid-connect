<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Jose\Component\Checker\ClaimChecker;
use Jose\Component\Checker\InvalidClaimException;

final class NonceChecker implements ClaimChecker
{
    private const CLAIM_NAME = 'nonce';
    private ?string $nonce;

    public function __construct(?string $nonce = null)
    {
        $this->nonce = $nonce;
    }

    public function checkClaim($value): void
    {
        if ($this->nonce !== null && $value !== $this->nonce) {
            throw new InvalidClaimException('Invalid nonce.', self::CLAIM_NAME, $value);
        }
    }

    public function supportedClaim(): string
    {
        return self::CLAIM_NAME;
    }
}
