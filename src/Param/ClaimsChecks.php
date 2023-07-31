<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Param;

use Jose\Component\Checker\ClaimChecker;

final class ClaimsChecks
{
    private array $mandatoryClaims;
    private array $checkers;

    /**
     * @param string[] $mandatoryClaims
     * @param ClaimChecker[] $checkers
     */
    public function __construct(array $mandatoryClaims = [], array $checkers = [])
    {
        $this->mandatoryClaims = $mandatoryClaims;
        $this->checkers = $checkers;
    }

    /**
     * @return string[]
     */
    public function mandatoryClaims(): array
    {
        return $this->mandatoryClaims;
    }

    /**
     * @return ClaimChecker[]
     */
    public function checkers(): array
    {
        return $this->checkers;
    }
}
