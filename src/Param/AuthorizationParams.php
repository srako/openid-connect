<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Param;

final class AuthorizationParams extends Params
{
    public const SCOPE = 'scope';
    public const STATE = 'state';
    public const NONCE = 'nonce';
    public const RESPONSE_TYPE = 'response_type';
}
