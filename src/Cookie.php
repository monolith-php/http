<?php namespace Monolith\Http;

final class Cookie
{
    /** @var string */
    private $name;
    /** @var string */
    private $value;
    /** @var int */
    private $expiresUnixTimestamp;
    /** @var string */
    private $path;
    /** @var string */
    private $domain;
    /** @var bool */
    private $secure;
    /** @var bool */
    private $httpOnly;

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function expiresUnixTimestamp(): int
    {
        return $this->expiresUnixTimestamp;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function secure(): bool
    {
        return $this->secure;
    }

    public function httpOnly(): bool
    {
        return $this->httpOnly;
    }

    public function __construct(string $name, string $value, int $expiresUnixTimestamp = 0, $path = '/', $domain = '', $secure = false, $httpOnly = false)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expiresUnixTimestamp = $expiresUnixTimestamp;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }
}