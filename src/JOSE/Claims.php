<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\JOSE;

use Srako\OpenIDConnect\Param\Params;
use Srako\OpenIDConnect\Util\JWT;
use stdClass;

final class Claims extends Params
{
    /**
     * payload转换claims
     * @param stdClass $payload
     * @return self
     */
    public static function fromClass(stdClass $payload): self
    {
        return new self(json_decode(json_encode($payload), true));
    }

    public static function fromToken(string $token): self
    {
        return new self(JWT::claims($token));
    }
}
