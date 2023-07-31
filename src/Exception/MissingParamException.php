<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Exception;

class MissingParamException extends RuntimeException
{
    public function __construct(string $key)
    {
        parent::__construct("Missing key \"$key\"");
    }
}
