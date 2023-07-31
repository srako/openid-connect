<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Token;

use Srako\OpenIDConnect\Param\ClaimsChecks;

interface TokenVerifierInterface
{
    public function verify(string $token, ClaimsChecks $checks): void;
}
