<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Param;

final class CallbackChecks
{
    private  ?string $state;
    private  ?string $nonce;
    public function __construct(?string $state = null, ?string $nonce = null)
    {
        $this->state = $state;
        $this->nonce = $nonce;
    }

    public function state(): ?string
    {
        return $this->state;
    }

    public function nonce(): ?string
    {
        return $this->nonce;
    }
}
