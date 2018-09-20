<?php namespace Monolith\Http;

interface IPAddress {
    public function equals(IPAddress $that): bool;
    public function toString(): string;
    public function __toString(): string;
}