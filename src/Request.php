<?php namespace Monolith\Http;

use Monolith\Collections\Map;
use function rawurldecode;

final class Request
{
    /** @var Map */
    private $get;
    /** @var Map */
    private $post;
    /** @var Map */
    private $server;
    /** @var Map */
    private $files;
    /** @var Map */
    private $cookies;
    /** @var Map */
    private $env;
    /** @var Map */
    private $parameters;

    public function __construct(Map $get, Map $post, Map $server, Map $files, Map $cookies, Map $env, Map $parameters = null)
    {
        if ($parameters == null) {
            $parameters = new Map;
        }

        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->env = $env;
        $this->parameters = $parameters;
    }

    public static function fromGlobals(): Request
    {
        return new static(new Map($_GET), new Map($_POST), new Map($_SERVER), new Map($_FILES), new Map($_COOKIE), new Map($_ENV), new Map());
    }

    public function addParameters(Map $params)
    {
        return new static(
            $this->get,
            $this->post,
            $this->server,
            $this->files,
            $this->cookies,
            $this->env,
            $this->parameters->merge($params)
        );
    }

    public function param(string $key)
    {
        return $this->parameters->get($key);
    }

    public function get(string $key)
    {
        return $this->get->get($key);
    }

    public function post(string $key)
    {
        return $this->post->get($key);
    }

    public function server(string $key)
    {
        return $this->server->get($key);
    }

    // i'm aware that many of these are not strings, but we'll hit that hump later
    public function file(string $key)
    {
        return $this->files->get($key);
    }

    public function cookie(string $key)
    {
        return $this->cookies->get($key);
    }

    public function env(string $key)
    {
        return $this->env->get($key);
    }

    public function uri()
    {
        return $this->server('REQUEST_URI');
    }

    public function method()
    {
        if (strtolower($this->server('REQUEST_METHOD')) == 'head') {
            return 'get';
        }

        return strtolower($this->server('REQUEST_METHOD'));
    }

    public function clientIP(): IpAddress
    {
        $ipAddress = $this->server('REMOTE_ADDR');

        if (Ipv4::isValid($ipAddress)) {
            return new Ipv4($ipAddress);
        }

        if (Ipv6::isValid($ipAddress)) {
            return new Ipv6($ipAddress);
        }

        throw new CanNotParseClientIp($ipAddress);
    }

    public function rawDecodedUri()
    {
        return rawurldecode($this->uri());
    }

    public function isSecure(): bool
    {
        return ! empty($this->server('HTTPS'));
    }

    public function scheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function serialize()
    {
        return [
            'get'      => $this->get->toArray(),
            'post'     => $this->post->toArray(),
            'server'   => $this->server->toArray(),
            'files'    => $this->files->toArray(),
            'cookies'  => $this->cookies->toArray(),
            'env'      => $this->env->toArray(),
            'clientIP' => (string) $this->clientIP(),
            'method'   => $this->method(),
            'isSecure' => $this->isSecure(),
            'scheme'   => $this->scheme(),
        ];
    }
}