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
    private $env;
    /** @var Map */
    private $cookies;

    private function __construct() {}

    public static function fromGlobals(): Request {
        $r = new static;
        $r->query = new Map($_GET);
        $r->input = new Map($_POST);
        $r->server = new Map($_SERVER);
        $r->files = new Map($_FILES);
        $r->env = new Map($_ENV);
        $r->cookies = new Map($_COOKIE);
        return $r;
    }

    public function query(string $key): string {
        return $this->query->get($key);
    }

    public function input(string $key): string {
        return $this->input->get($key);
    }

    public function server(string $key): string {
        return $this->server->get($key);
    }

    public function file(string $key): string {
        return $this->files->get($key);
    }

    public function env(string $key): string {
        return $this->env->get($key);
    }

    public function cookie(string $key): string {
        return $this->cookies->get($key);
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

    public function isSecureConnection(): bool {
        return ! $this->server('HTTPS');
    }
}