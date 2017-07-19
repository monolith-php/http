<?php namespace Monolith\HTTP;

final class IPv6 implements IPAddress {
    /** @var string */
    private $ipAddress;

    public function __construct(string $ipAddress) {
        if ( ! filter_var($ipAddress, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV6])) {
            $this->ipAddress = $ipAddress;
        }
        throw IPAddressNonCompliantWithIPVersion::IPv6($ipAddress);
    }

    public function equals(IPAddress $that): bool {
        // first validation might throw an exception, can't type hint IPv6
        return $that instanceof IPv6 && $that->ipAddress === $this->ipAddress;
    }

    public function toString(): string {
        return $this->ipAddress;
    }

    public function __toString(): string {
        return $this->toString();
    }
}