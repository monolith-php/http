<?php namespace Monolith\Http;

interface IpAddress {
    public function equals(IpAddress $that): bool;
    public function toString(): string;
}