<?php namespace Monolith\Http;

use Monolith\Collections\Dict;

final class Request
{
    /** @var string */
    private $body;
    /** @var Dict */
    private $get;
    /** @var Dict */
    private $post;
    /** @var Dict */
    private $server;
    /** @var Dict */
    private $files;
    /** @var Dict */
    private $cookies;
    /** @var Dict */
    private $env;
    /** @var Dict */
    private $headers;
    /** @var Dict */
    private $parameters;

    public function __construct(
        string $body,
        Dict $get,
        Dict $post,
        Dict $server,
        Dict $files,
        Dict $cookies,
        Dict $env,
        Dict $headers,
        Dict $parameters = null
    ) {
        if ($parameters == null) {
            $parameters = new Dict;
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
            file_get_contents('php://input') ?? '',
            new Dict($_GET),
            new Dict($_POST),
            new Dict($_SERVER),
            new Dict($_FILES),
            new Dict($_COOKIE),
            new Dict($_ENV),
            new Dict(self::getHeaders()),
            new Dict()
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

    public function addParameters(Dict $params): Request
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

    public function parameters(): Dict
    {
        return $this->parameters;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function get(): Dict
    {
        return $this->get;
    }

    public function post(): Dict
    {
        return $this->post;
    }

    public function server(): Dict
    {
        return $this->server;
    }

    public function files(): Dict
    {
        return $this->files;
    }

    public function cookies(): Dict
    {
        return $this->cookies;
    }

    public function env(): Dict
    {
        return $this->env;
    }

    public function headers(): Dict
    {
        return $this->headers;
    }

    public function uri()
    {
        $uri = $this->server->get('REQUEST_URI');

        $uriWithoutQueryString =
            stristr($uri, '?') ? strstr($uri, '?', true) : $uri;

        return urldecode(
            $uriWithoutQueryString
        );
    }

    public function rawUri()
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