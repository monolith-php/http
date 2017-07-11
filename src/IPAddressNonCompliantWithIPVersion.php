<?php namespace Monolith\HTTP;

use Exception;

final class IPAddressNonCompliantWithIPVersion extends Exception {
    public function __construct(string $ipAddress, string $ipVersion) {
        parent::__construct("IP address [{$ipAddress}] was non-compliant with {$ipVersion}.");
    }

    public static function IPv4(string $ipAddress): IPAddressNonCompliantWithIPVersion {
        return new static($ipAddress, 'IPv4');
    }

    public static function IPv6(string $ipAddress): IPAddressNonCompliantWithIPVersion {
        return new static($ipAddress, 'IPv6');
    }
}