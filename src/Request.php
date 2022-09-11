<?php namespace Monolith\Http;

use Monolith\Collections\Dictionary;

final class Request
{
    public function __construct(
        private readonly string $body,
        private readonly Dictionary $get,
        private readonly Dictionary $post,
        private readonly Dictionary $server,
        private readonly Dictionary $files,
        private readonly Dictionary $cookies,
        private readonly Dictionary $env,
        private readonly Dictionary $headers,
        private ?Dictionary $appParameters = null
    ) {
        $this->appParameters ??= Dictionary::empty();
    }

    public function addAppParameters(Dictionary $newAppParameters): Request
    {
        return new self(
            $this->body,
            $this->get,
            $this->post,
            $this->server,
            $this->files,
            $this->cookies,
            $this->env,
            $this->headers,
            $this->appParameters->merge($newAppParameters)
        );
    }

    public function appParameters(): Dictionary
    {
        return $this->appParameters;
    }

    public function urlParameters(): Dictionary
    {
        parse_str($this->server->get('QUERY_STRING'), $urlParameters);
        return Dictionary::of($urlParameters);
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

    public function uri(): string
    {
        $uri = $this->server->get('REQUEST_URI');

        $uriWithoutQueryString =
            stristr($uri ?? '', '?') ? strstr($uri, '?', true) : $uri;

        return urldecode(
            $uriWithoutQueryString ?? ''
        );
    }

    public function rawUri()
    {
        return $this->server->get('REQUEST_URI');
    }

    public function method(): string
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

    public function scheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function serialize(): array
    {
        return [
            'body' => $this->body,
            'get' => $this->get->toArray(),
            'post' => $this->post->toArray(),
            'server' => $this->server->toArray(),
            'parameters' => $this->appParameters->toArray(),
            'headers' => $this->headers->toArray(),
            'files' => $this->files->toArray(),
            'cookies' => $this->cookies->toArray(),
            'env' => $this->env->toArray(),
            'clientIP' => $this->clientIP()->toString(),
            'method' => $this->method(),
            'isSecure' => $this->isSecure(),
            'scheme' => $this->scheme(),
        ];
    }

    public static function fromGlobals(): Request
    {
        return new self(
            file_get_contents('php://input'),
            Dictionary::of($_GET),
            Dictionary::of($_POST),
            Dictionary::of($_SERVER),
            Dictionary::of($_FILES)->map(
                fn($field, $file) => [$field => File::fromRequest($file)]
            ),
            Dictionary::of($_COOKIE),
            Dictionary::of($_ENV),
            Dictionary::of(self::getHeaders()),
            Dictionary::empty()
        );
    }

    private static function getHeaders(): bool|array
    {
        if ( ! function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (str_starts_with($name, 'HTTP_')) {
                    $headers[str_replace(
                        ' ', '-',
                        ucwords(strtolower(str_replace('_', ' ', substr($name, 5))))
                    )] = $value;
                }
            }
            return $headers;
        }
        return getallheaders();
    }
}