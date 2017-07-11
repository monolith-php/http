<?php namespace Monolith\HTTP;

class Request {
    /** @var string */
    private $uri;
    /** @var string */
    private $method;

    private function __construct() {}

    public static function fromGlobals(): Request {
        $r = new static;
        $r->uri = $_SERVER['REQUEST_URI'];
        $r->method = strtoupper($_SERVER['REQUEST_METHOD']);
        return $r;
    }

    public function uri(): string {
        return $this->uri;
    }

    public function method(): string {
        return $this->method;
    }
}