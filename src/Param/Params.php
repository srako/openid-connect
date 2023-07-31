<?php

declare(strict_types=1);

namespace Srako\OpenIDConnect\Param;

class Params
{
    private array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    public function get(string $key, $default = null)
    {
        return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
    }

    public function getString(string $key, ?string $default = null): string
    {
        return (string)$this->get($key, $default);
    }

    public function getInt(string $key, ?int $default = null): int
    {
        return (int)$this->get($key, $default);
    }

    public function getBool(string $key, ?bool $default = null): bool
    {
        return (bool)$this->get($key, $default);
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->parameters;
    }
}
