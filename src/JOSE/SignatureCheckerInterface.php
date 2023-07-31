<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

interface SignatureCheckerInterface
{
    public function check(string $token): void;
}
