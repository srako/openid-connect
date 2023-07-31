<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Srako\OpenIDConnect\Param\ClaimsChecks;

interface ClaimsCheckerInterface
{
    public function check(Claims $claims, ClaimsChecks $checks): void;
}
