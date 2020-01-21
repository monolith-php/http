<?php namespace Monolith\Http;

use Monolith\Collections\Dictionary;
use Monolith\Collections\Collection;

final class Response
{
    /** @var string */
    private $code;
    /** @var string */
    private $codeString;
    /** @var string */
    private $body;
    /** @var Collection */
    private $cookies;
    /** @var Dictionary */
    private $headers;

    protected function __construct(
        string $code,
        $codeString,
        $body = '',
        Collection $cookies = null,
        Dictionary $headers = null
    ) {
        $this->body = $body;
        $this->code = $code;
        $this->codeString = $codeString;
        $this->cookies = $cookies ?? Collection::empty();
        $this->headers = $headers ?? Dictionary::empty();
    }

    public static function ok(string $body = '')
    {
        return new static('200', 'OK', $body);
    }

    public static function created(string $body = '')
    {
        return new static('201', 'Created', $body);
    }

    public static function noContent(string $body = '')
    {
        return new static('204', 'No Content', $body);
    }

    public static function redirect($url)
    {
        return new static(
            '302', 'Found', '', Collection::empty(), Dictionary::of(
            [
                'Location' => $url,
            ])
        );
    }

    public static function badRequest(string $body = '')
    {
        return new static('400', 'Bad Request', $body);
    }

    public static function unauthorized(string $body = '')
    {
        return new static('401', 'Unauthorized', $body);
    }

    public static function notFound(string $body = '')
    {
        return new static('404', 'Not Found', $body);
    }

    public static function unprocessable(string $body = '')
    {
        return new static('422', 'Unprocessable Entity', $body);
    }

    public static function tooManyRequests(string $body = '')
    {
        return new static('429', 'Too Many Requests', $body);
    }

    // this whole class needs to be reviewed
    public function send()
    {
        $this->sendHeaders();

        echo $this->body;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    public function sendHeaders(): void
    {
        $this->cookies->each(function($cookie) {
            /** @var Cookie $cookie */
            setcookie(
                $cookie->name(),
                $cookie->value(),
                $cookie->expiresUnixTimestamp(),
                $cookie->path(),
                $cookie->domain(),
                $cookie->secure(),
                $cookie->httpOnly()
            );
        });

        header("HTTP/1.1 {$this->code()} {$this->codeString()}", true, (int)$this->code());

        foreach ($this->headers->toArray() as $name => $value) {
            header(trim($name) . ': ' . trim($value));
        }
    }

    public function code(): string
    {
        return $this->code;
    }

    public function codeString(): string
    {
        return $this->codeString;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function cookies(): Collection
    {
        return $this->cookies;
    }

    public function headers(): Dictionary
    {
        return $this->headers;
    }

    public function withHeader($name, $value): Response
    {
        return new Response(
            $this->code,
            $this->codeString,
            $this->body,
            $this->cookies,
            $this->headers->add($name, $value)
        );
    }

    public function withCookie(Cookie $cookie): Response
    {
        return new Response(
            $this->code,
            $this->codeString,
            $this->body,
            $this->cookies->add($cookie)
        );
    }
}
