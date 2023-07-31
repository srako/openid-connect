<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Authentication;

interface AuthenticationMethod
{
    public function getMethod(): string;
}
