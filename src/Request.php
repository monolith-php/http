<?php namespace Monolith\Http;

use Monolith\Collections\Map;
use function rawurldecode;

final class Request
{
    /** @var string */
    private $body;
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
    private $headers;
    /** @var Map */
    private $parameters;

    public function __construct(
        string $body,
        Map $get,
        Map $post,
        Map $server,
        Map $files,
        Map $cookies,
        Map $env,
        Map $headers,
        Map $parameters = null
    ) {
        if ($parameters == null) {
            $parameters = new Map;
        }
        $this->body = $body;
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->env = $env;
        $this->headers = $headers;
        $this->parameters = $parameters;
    }

    public static function fromGlobals(): Request
    {
        return new static(
            file_get_contents('php://input'),
            new Map($_GET),
            new Map($_POST),
            new Map($_SERVER),
            new Map($_FILES),
            new Map($_COOKIE),
            new Map($_ENV),
            new Map(self::getHeaders()),
            new Map()
        );
    }

    private static function getHeaders()
    {
        if ( ! function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-',
                        ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
        return getallheaders();
    }

    public function addParameters(Map $params): Request
    {
        return new static(
            $this->body,
            $this->get,
            $this->post,
            $this->server,
            $this->files,
            $this->cookies,
            $this->env,
            $this->headers,
            $this->parameters->merge($params)
        );
    }

    public function parameters(): Map
    {
        return $this->parameters;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function get(): Map
    {
        return $this->get;
    }

    public function post(): Map
    {
        return $this->post;
    }

    public function server(): Map
    {
        return $this->server;
    }

    public function files(): Map
    {
        return $this->files;
    }

    public function cookies(): Map
    {
        return $this->cookies;
    }

    public function env(): Map
    {
        return $this->env;
    }

    public function headers(): Map
    {
        return $this->headers;
    }

    public function uri()
    {
        return $this->server->get('REQUEST_URI');
    }

    public function method()
    {
        if (strtolower($this->server->get('REQUEST_METHOD')) == 'head') {
            return 'get';
        }

        return strtolower($this->server->get('REQUEST_METHOD'));
    }

    public function clientIP(): IpAddress
    {
        $ipAddress = $this->server->get('REMOTE_ADDR');

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

    public function rawDecodedUriWithoutQueryString()
    {
        return
            stristr($this->rawDecodedUri(), '?')
                ? strstr($this->rawDecodedUri(), '?', true)
                : $this->rawDecodedUri();
    }

    public function isSecure(): bool
    {
        return ! empty($this->server->get('HTTPS'));
    }

    public function scheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function serialize()
    {
        return [
            'body'       => $this->body,
            'get'        => $this->get->toArray(),
            'post'       => $this->post->toArray(),
            'server'     => $this->server->toArray(),
            'parameters' => $this->parameters->toArray(),
            'headers'    => $this->headers->toArray(),
            'files'      => $this->files->toArray(),
            'cookies'    => $this->cookies->toArray(),
            'env'        => $this->env->toArray(),
            'clientIP'   => $this->clientIP()->toString(),
            'method'     => $this->method(),
            'isSecure'   => $this->isSecure(),
            'scheme'     => $this->scheme(),
        ];
    }
}