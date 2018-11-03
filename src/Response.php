<?php namespace Monolith\Http;

use Monolith\Collections\MutableMap;

final class Response
{
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

    public static function redirect($url)
    {
        $headers = new MutableMap([
            'Location' => $url,
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache'
        ]);
        return new static('302', 'Found', '', [], $headers);
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
    /** @var array */
    private $cookies;
    /** @var MutableMap  */
    private $additionalHeaders;

    protected function __construct($code, $codeString, $body = '', $cookies = [], MutableMap $additionalHeaders = null)
    {
        $this->body = $body;
        $this->code = $code;
        $this->codeString = $codeString;
        $this->cookies = $cookies;
        $this->additionalHeaders = $additionalHeaders ?? new MutableMap;;
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

    public function additionalHeaders(): MutableMap
    {
        return $this->additionalHeaders;
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

        foreach ($this->additionalHeaders->toArray() as $name => $value) {
            header("{$name}: {$value}");
        }
    }
}
