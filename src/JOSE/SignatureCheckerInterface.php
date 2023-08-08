<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use stdClass;

interface SignatureCheckerInterface
{
    public function check(string $token): stdClass;
}
