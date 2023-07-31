<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Srako\OpenIDConnect\Param\Params;
use Srako\OpenIDConnect\Util\JWT;

final class Claims extends Params
{
    public static function fromToken(string $token): self
    {
        return new self(JWT::claims($token));
    }
}
