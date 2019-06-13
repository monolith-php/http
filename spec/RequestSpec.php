<?php namespace spec\Monolith\Http;

use Monolith\Collections\Map;
use Monolith\Http\Ipv4;
use PhpSpec\ObjectBehavior;

class RequestSpec extends ObjectBehavior
{
    function let()
    {
        $body = 'raw body';
        $get = new Map();
        $input = new Map();
        $server = new Map();
        $files = new Map();
        $cookies = new Map();
        $env = new Map();
        $headers = new Map();

        $this->beConstructedWith($body, $get, $input, $server, $files, $cookies, $env, $headers);
    }

    function it_can_be_constructed_from_globals()
    {
        $_GET['a0'] = 'b0';
        $_POST['a1'] = 'b1';
        $_SERVER['a2'] = 'b2';
        $_SERVER['REQUEST_URI'] = 'my uri';
        $_SERVER['REQUEST_METHOD'] = 'my method';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_FILES['a3'] = 'b3';
        $_COOKIE['a4'] = 'b4';
        $_ENV['a5'] = 'b5';

        if ( ! function_exists('getallheaders')) {
            $_SERVER['HTTP_test'] = 'cat';
        } else {
            header('test: cat');
        }

        $request = $this::fromGlobals();

        $request->get()->get('a0')->shouldBe('b0');
        $request->post()->get('a1')->shouldBe('b1');
        $request->server()->get('a2')->shouldBe('b2');
        $request->files()->get('a3')->shouldBe('b3');
        $request->cookies()->get('a4')->shouldBe('b4');
        $request->env()->get('a5')->shouldBe('b5');
        $request->headers()->get('Test')->shouldBe('cat');
        $request->uri()->shouldBe('my uri');
        $request->method()->shouldBe('my method');

        $request->clientIP()->shouldHaveType(Ipv4::class);
        $request->clientIp()->toString()->shouldBe('127.0.0.1');
    }

    public function it_can_be_enriched_with_parameters()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $request = $this::fromGlobals();

        $request = $request->addParameters(new Map(['a' => 1, 'b' => 2]));
        $request->parameters()->get('a')->shouldBe(1);
        $request->parameters()->get('b')->shouldBe(2);

        $serialized = $request->serialize();

        $serialized['parameters']->shouldHaveCount(2);
    }

    public function it_can_provided_decoded_urls_without_query_strings()
    {
        $_SERVER['REQUEST_URI'] = 'my-uri';
        $request = $this::fromGlobals();
        $request->rawDecodedUriWithoutQueryString()->shouldBe('my-uri');

        $_SERVER['REQUEST_URI'] = 'again?dogs=cats';
        $request = $this::fromGlobals();
        $request->rawDecodedUriWithoutQueryString()->shouldBe('again');

        $_SERVER['REQUEST_URI'] = 'hats%20again?dogs=cats';
        $request = $this::fromGlobals();
        $request->rawDecodedUriWithoutQueryString()->shouldBe('hats again');
    }

    public function it_interprets_query_strings_as_get_arguments()
    {
        $_SERVER['REQUEST_URI'] = 'my-uri';
        $_SERVER['QUERY_STRING'] = 'cats=dogs&hats=clogs';

        $request = $this::fromGlobals();

        $request->get()->get('cats')->shouldBe('dogs');
        $request->get()->get('hats')->shouldBe('clogs');
    }
}
