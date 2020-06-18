<?php namespace Monolith\Http;

final class ByteSize
{
    private int $bytes;

    private function __construct(int $bytes)
    {
        $this->bytes = $bytes;
    }

    public function bits(): int
    {
        return $this->bytes * 8;
    }

    public function bytes(): int
    {
        return $this->bytes;
    }

    public function kilobytes(): float
    {
        return $this->bytes() / 1024;
    }

    public function megabytes(): float
    {
        return $this->kilobytes() / 1024;
    }

    public function gigabytes(): float
    {
        return $this->megabytes() / 1024;
    }

    public function terabytes(): float
    {
        return $this->gigabytes() / 1024;
    }

    public function petabytes(): float
    {
        return $this->terabytes() / 1024;
    }

    public static function fromBytes(float $bytes): self
    {
        $roundedValue = ceil($bytes);
        
        if($roundedValue > PHP_INT_MAX) {
            throw new ByteSizeIsTooLarge($roundedValue);
        }

        return new static($roundedValue);
    }

    public static function fromKilobytes(float $kilobytes): self
    {
        return static::fromBytes($kilobytes * 1024);
    }

    public static function fromMegabytes(float $megabytes): self
    {
        return static::fromKilobytes($megabytes * 1024);
    }

    public static function fromGigabytes(float $gigabytes): self
    {
        return static::fromMegabytes($gigabytes * 1024);
    }

    public static function fromTerabytes(float $terabytes): self
    {
        return static::fromGigabytes($terabytes * 1024);
    }

    public static function fromPetabytes(float $petabytes): self
    {
        return static::fromTerabytes($petabytes * 1024);
    }
}