<?php namespace Monolith\Http;

use Closure;
use Monolith\Collections\Dictionary;
use Monolith\Collections\Collection;

/**
 * Ok so... The reason that this is done this way is to create
 * a simple facade for all responses. Otherwise I'd separate into
 * StreamResponse and have an interface etc. I'm still toying with
 * the api.
 */
final class Response
{
    public function __construct(
        private readonly string $code,
        private readonly string $codeString,
        private readonly mixed $body = '',
        private ?Collection $cookies = null,
        private ?Dictionary $headers = null,
        private readonly ?Closure $streamFunction = null
    ) {
        $this->cookies = $cookies ?? Collection::empty();
        $this->headers = $headers ?? Dictionary::empty();
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

    public function withHeader($name, $value): self
    {
        return new Response(
            $this->code,
            $this->codeString,
            $this->body,
            $this->cookies,
            $this->headers->add($name, $value)
        );
    }

    public function withCookie(Cookie $cookie): self
    {
        return new Response(
            $this->code,
            $this->codeString,
            $this->body,
            $this->cookies->add($cookie),
            $this->headers
        );
    }

    public function send(): void
    {
        if (is_callable($this->streamFunction)) {
            $this->streamResponse($this->streamFunction);
            return;
        }

        $this->sendResponse();
    }

    private function streamResponse(): void
    {
        $this->sendCookies();

        header("HTTP/1.1 {$this->code()} {$this->codeString()}", true, (int)$this->code());

        # configure php for streaming
        ini_set('zlib.output_compression', 0);
        ini_set('output_buffering', 'Off');
        ini_set('output_handler', '');
        ini_set('implicit_flush', 1);
        ob_implicit_flush(true);
        if (ob_get_contents()) ob_end_clean();

        $this->headers = $this->headers
            ->add('Cache-Control', 'no-cache')
            ->add('Content-Type', 'text/event-stream')
            # these last two are for punching through nginx's attempts
            # to use gzip or output buffering
            ->add('Content-Encoding', 'identity')
            ->add('X-Accel-Buffering', 'no');

        $this->sendHeaders();

        ob_implicit_flush(true);

        ($this->streamFunction)(
            function (string $chunk) {
                echo "{$chunk}\r\n";
            }
        );
    }

    private function sendCookies(): void
    {
        $this->cookies->each(
            function ($cookie) {
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
            }
        );
    }

    public function code(): string
    {
        return $this->code;
    }

    public function codeString(): string
    {
        return $this->codeString;
    }

    private function sendHeaders(): void
    {
        if ( ! $this->headers->has('Content-Type')) {
            $this->headers = $this->headers->add('Content-Type', 'text/html');
        }

        foreach ($this->headers->toArray() as $name => $value) {
            header(trim($name) . ': ' . trim($value));
        }
    }

    private function sendResponse(): void
    {
        $this->sendCookies();

        header("HTTP/1.1 {$this->code()} {$this->codeString()}", true, (int)$this->code());

        if ( ! $this->headers->has('Content-Length')) {
            $this->headers = $this->headers->add('Content-Length', strlen($this->body));
        }

        $this->sendHeaders();

        echo $this->body;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * The callable is a closure that has a single argument that is a
     * send function.
     *
     * function($send) {
     *     while(true) {
     *         $string = makeData();
     *         $send($string)
     *         sleep(1);
     *     }
     * }
     *
     * @param callable $streamFunction
     * @return static
     */
    public static function stream(callable $streamFunction): self
    {
        return new self('', '', '', null, null, $streamFunction);
    }

    public static function ok(string $body = ''): self
    {
        return new self('200', 'OK', $body);
    }

    public static function created(string $body = ''): self
    {
        return new self('201', 'Created', $body);
    }

    public static function noContent(string $body = ''): self
    {
        return new self('204', 'No Content', $body);
    }

    public static function redirect($url): self
    {
        return new self(
            '302', 'Found', '', Collection::empty(), Dictionary::of(
            [
                'Location' => $url,
            ])
        );
    }

    public static function badRequest(string $body = ''): self
    {
        return new self('400', 'Bad Request', $body);
    }

    public static function unauthorized(string $body = ''): self
    {
        return new self('401', 'Unauthorized', $body);
    }

    public static function notFound(string $body = ''): self
    {
        return new self('404', 'Not Found', $body);
    }

    public static function unprocessable(string $body = ''): self
    {
        return new self('422', 'Unprocessable Entity', $body);
    }

    public static function tooManyRequests(string $body = ''): self
    {
        return new self('429', 'Too Many Requests', $body);
    }
}
