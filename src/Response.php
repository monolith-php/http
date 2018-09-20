<?php namespace Monolith\Http;

class Response {

    public static function code200($body) {
        return new static('200', $body);
    }

    private $code;
    private $body;

    protected function __construct($code, $body) {

        $this->body = $body;
        $this->code = $code;
    }

    // this whole class needs to be reviewed
    public function send() {

        header('HTTP/1.1 ' . $this->codeString(), true, 200);

        echo $this->body;

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    public function code() {
        return $this->code;
    }

    public function body() {
        return $this->body;
    }

    public function codeString(): string {

        switch ($this->code) {
            case '200':
                return '200 OK';
        }
    }
}