<?php namespace Monolith\Http;

final class Response
{
    /** @var array */
    private $cookies;

    public static function ok($body)
    {
        return new static('200', 'OK', $body);
    }

    public static function created()
    {
        return new static('201', 'Created');
    }

    public static function noContent()
    {
        return new static('204', 'No Content');
    }

    public static function movedPermanently($url)
    {
        return new static('301', 'Moved Permanently', $url);
    }

    public static function redirect($url)
    {
        return new static('307', 'See Other', $url);
    }

    public static function badRequest($body)
    {
        return new static('400', 'Bad Request', $body);
    }

    public static function unauthorized()
    {
        return new static('401', 'Unauthorized');
    }

    public static function notFound()
    {
        return new static('404', 'Not Found');
    }

    public static function tooManyRequests()
    {
        return new static('429', 'Too Many Requests');
    }

    private $code;
    private $codeString;
    private $body;

    protected function __construct($code, $codeString, $body = '', $cookies = [])
    {
        $this->body = $body;
        $this->code = $code;
        $this->codeString = $codeString;
        $this->cookies = $cookies;
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

    public function code()
    {
        return $this->code;
    }

    public function body()
    {
        return $this->body;
    }

    public function codeString(): string
    {
        return $this->codeString;
    }

    public function cookies(): array
    {
        return $this->cookies;
    }

    public function addCookie(Cookie $cookie): Response
    {
        $newCookies = $this->cookies;
        $newCookies[] = $cookie;

        return new Response($this->code, $this->codeString, $this->body, $newCookies);
    }

    public function sendHeaders(): void
    {
        /** @var Cookie $cookie */
        foreach ($this->cookies as $cookie) {
            setcookie($cookie->name(), $cookie->value(), $cookie->expiresUnixTimestamp(), $cookie->path(), $cookie->domain(), $cookie->secure(), $cookie->httpOnly());
        }

        header("HTTP/1.1 {$this->code()} {$this->codeString()}", true, $this->code());
    }
}
