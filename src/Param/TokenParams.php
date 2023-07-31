<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Param;

use Srako\OpenIDConnect\Grant\GrantType;

final class TokenParams extends Params
{
    public const CODE = 'code';
    public const GRANT_TYPE = 'grant_type';
    public const SCOPE = 'scope';

    private GrantType $grantType;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(GrantType $grantType, array $parameters = [])
    {
        $this->grantType = $grantType;
        parent::__construct($parameters);
    }

    public function grantType(): GrantType
    {
        return $this->grantType;
    }
}
