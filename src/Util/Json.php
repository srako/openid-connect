<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Util;

use JsonException;
use Safe\Exceptions\JsonException as SafeJsonException;

use function Safe\json_decode;
use function Safe\json_encode;

final class Json
{
    /**
     * @param mixed[] $payload
     *
     * @throws SafeJsonException|JsonException
     */
    public static function encode(array $payload): string
    {
        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    /**
     * @return mixed[]
     *
     * @throws SafeJsonException|JsonException
     */
    public static function decode(string $json): array
    {
        // the json_last_error will not reset when using JSON_THROW_ON_ERROR flag
        \json_decode('[]');

        $result = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($result)) {
            throw new JsonException('Invalid JSON');
        }

        return $result;
    }
}
