<?php namespace Monolith\HTTP;

use Monolith\Collections\Map;

class Request {
    /** @var Map */
    private $query;
    /** @var Map */
    private $input;
    /** @var Map */
    private $server;
    /** @var Map */
    private $files;
    /** @var Map */
    private $cookies;
    /** @var Map */
    private $env;

    private function __construct() {}

    public static function fromGlobals(): Request {
        $r = new static;
        $r->query = new Map($_GET);
        $r->input = new Map($_POST);
        $r->server = new Map($_SERVER);
        $r->files = new Map($_FILES);
        $r->cookies = new Map($_COOKIE);
        $r->env = new Map($_ENV);
        return $r;
    }

    public function query(string $key): ?string {
        return $this->query->has($key) ? (string) $this->query->get($key) : null;
    }

    public function input(string $key): ?string {
        return $this->input->has($key) ? (string) $this->input->get($key) : null;
    }

    public function server(string $key): ?string {
        return $this->server->has($key) ? (string) $this->server->get($key) : null;
    }

    public function file(string $key) {
        return $this->files->has($key) ? $this->files->get($key) : null;
    }

    public function cookie(string $key) {
        return $this->cookies->has($key) ? $this->cookies->get($key) : null;
    }

    public function env(string $key): ?string {
        return $this->env->has($key) ? (string) $this->env->get($key) : null;
    }

    public function uri(): string {
        return $this->server('REQUEST_URI'); // needs to be a class
    }

    public function method(): string {
        return strtoupper($this->server('REQUEST_METHOD'));
    }

    public function clientIP(): IPAddress {
        return new IPv4($this->server('REMOTE_ADDR'));
    }

    public function isSecure(): bool {
        return ! $this->server('HTTPS');
    }

    public function scheme(): string {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function __debugInfo() {
        return [
            'query' => $this->query->toArray(),
            'input' => $this->input->toArray(),
            'server' => $this->server->toArray(),
            'files' => $this->files->toArray(),
            'cookies' => $this->cookies->toArray(),
            'env' => $this->env->toArray(),
            'clientIP' => (string) $this->clientIP(),
            'method' => $this->method(),
            'isSecure' => $this->isSecure(),
            'scheme' => $this->scheme(),
        ];
    }
}