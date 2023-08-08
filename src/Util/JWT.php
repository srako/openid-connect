<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Util;

use InvalidArgumentException;
use JsonException;
use UnexpectedValueException;

final class JWT extends \Firebase\JWT\JWT
{
    /**
     * @return array{header: array<string, mixed>, payload: array<string, mixed>, signature: string}
     *
     * @throws UnexpectedValueException
     */
    public static function parse(string $jwt): array
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            throw new UnexpectedValueException('Invalid JWT - wrong number of parts');
        }

        [$header, $payload, $signature] = $parts;

        $headerRaw = JWT::urlsafeB64Decode($header);
        if (null === ($header = JWT::jsonDecode($headerRaw))) {
            throw new UnexpectedValueException('Invalid header encoding');
        }

        $payloadRaw = JWT::urlsafeB64Decode($payload);
        if (null === ($payload = JWT::jsonDecode($payloadRaw))) {
            throw new UnexpectedValueException('Invalid claims encoding');
        }

        return [
            'header' => get_class_vars($header),
            'payload' => get_class_vars($payload),
            'signature' => JWT::urlsafeB64Decode($signature)
        ];
    }

    public static function validate(string $jwt): bool
    {
        try {
            self::parse($jwt);
            return true;
        } catch (UnexpectedValueException|InvalidArgumentException $exception) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function header(string $jwt): array
    {
        return self::parse($jwt)['header'];
    }

    /**
     * @return array<string, mixed>
     */
    public static function claims(string $jwt): array
    {
        return self::parse($jwt)['payload'];
    }

    /**
     * @return mixed
     */
    public static function claim(string $jwt, string $claim)
    {
        return self::claims($jwt)[$claim] ?? null;
    }

    public static function jsonToArray(string $input): array
    {
        $obj = \json_decode($input, true, 512, JSON_BIGINT_AS_STRING);

        if (\json_last_error()) {
            throw new JsonException(\json_last_error_msg());
        } elseif ($obj === null && $input !== 'null') {
            throw new JsonException('Null result with non-null input');
        }
        return $obj;
    }
}
