<?php namespace Monolith\Http;

use Monolith\Collections\Dictionary;

final class Request
{
    /** @var string */
    private $body;
    /** @var Dictionary */
    private $get;
    /** @var Dictionary */
    private $post;
    /** @var Dictionary */
    private $server;
    /** @var Dictionary */
    private $files;
    /** @var Dictionary */
    private $cookies;
    /** @var Dictionary */
    private $env;
    /** @var Dictionary */
    private $headers;
    /** @var Dictionary */
    private $parameters;

    public function __construct(
        string $body,
        Dictionary $get,
        Dictionary $post,
        Dictionary $server,
        Dictionary $files,
        Dictionary $cookies,
        Dictionary $env,
        Dictionary $headers,
        Dictionary $parameters = null
    ) {
        if ($parameters == null) {
            $parameters = new Dictionary;
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
            new Dictionary($_GET),
            new Dictionary($_POST),
            new Dictionary($_SERVER),
            new Dictionary($_FILES),
            new Dictionary($_COOKIE),
            new Dictionary($_ENV),
            new Dictionary(self::getHeaders()),
            new Dictionary()
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

    public function addParameters(Dictionary $params): Request
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

    public function parameters(): Dictionary
    {
        return $this->parameters;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function get(): Dictionary
    {
        return $this->get;
    }

    public function post(): Dictionary
    {
        return $this->post;
    }

    public function server(): Dictionary
    {
        return $this->server;
    }

    public function files(): Dictionary
    {
        return $this->files;
    }

    public function cookies(): Dictionary
    {
        return $this->cookies;
    }

    public function env(): Dictionary
    {
        return $this->env;
    }

    public function headers(): Dictionary
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