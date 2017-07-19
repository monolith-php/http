<?php namespace Monolith\HTTP;

final class IPv4 implements IPAddress {
    /** @var string */
    private $ipAddress;

    public function __construct(?string $ipAddress) {
        if ( ! filter_var($ipAddress, FILTER_VALIDATE_IP, ['flags' => FILTER_FLAG_IPV4])) {
            throw IPAddressNonCompliantWithIPVersion::IPv4($ipAddress);
        }
        $this->ipAddress = $ipAddress;
    }

    public function equals(IPAddress $that): bool {
        // first validation might throw an exception, can't type hint IPv4
        return $that instanceof IPv4 && $that->ipAddress === $this->ipAddress;
    }

    public function toString(): string {
        return $this->ipAddress;
    }

    public function __toString(): string {
        return $this->toString();
    }
}