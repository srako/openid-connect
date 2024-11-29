<?php

namespace Srako\OpenIDConnect\Util;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

class SystemClock implements ClockInterface
{

    /**
     * @inheritDoc
     */
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}