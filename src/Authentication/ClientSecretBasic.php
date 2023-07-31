<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Authentication;

final class ClientSecretBasic implements AuthenticationMethod
{
    public function getMethod(): string
    {
        return 'client_secret_basic';
    }
}
