<?php namespace Monolith\Http;

use Exception;

final class IPAddressNonCompliantWithIPVersion extends Exception {
    public function __construct(string $ipAddress, string $ipVersion) {
        parent::__construct("IP address [{$ipAddress}] is non-compliant with {$ipVersion}.");
    }

    public static function IPv4(string $ipAddress): IPAddressNonCompliantWithIPVersion {
        return new static($ipAddress, 'IPv4');
    }

    public static function IPv6(string $ipAddress): IPAddressNonCompliantWithIPVersion {
        return new static($ipAddress, 'IPv6');
    }

    public static function allVersions(string $ipAddress): IPAddressNonCompliantWithIPVersion {
        return new static($ipAddress, 'both IPv4 and IPv6');
    }
}