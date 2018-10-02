<?php namespace Monolith\Http;

final class Ipv6 implements IpAddress
{
    /** @var string */
    private $ipAddress;

    public function __construct(string $ipAddress)
    {
        if ( ! static::isValid($ipAddress)) {
            throw new Ipv6AddressIsNotValid($ipAddress);
        }

        $this->ipAddress = $ipAddress;
    }

    public static function isValid(string $ipAddress): bool
    {
        return filter_var($ipAddress, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV6]);
    }

    public function equals(IpAddress $that): bool
    {
        return get_class($this) === get_class($that) && $that->ipAddress === $this->ipAddress;
    }

    public function toString(): string
    {
        return $this->ipAddress;
    }
}