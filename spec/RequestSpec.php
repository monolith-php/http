<?php namespace spec\Monolith\Http;

use Monolith\Collections\Map;
use PhpSpec\ObjectBehavior;

class RequestSpec extends ObjectBehavior
{
    function let()
    {
        $get = new Map();
        $input = new Map();
        $server = new Map();
        $files = new Map();
        $cookies = new Map();
        $env = new Map();

        $this->beConstructedWith($get, $input, $server, $files, $cookies, $env);
    }

    function it_can_be_constructed_from_globals()
    {
        $_GET['a0'] = 'b0';
        $_POST['a1'] = 'b1';
        $_SERVER['a2'] = 'b2';
        $_FILES['a3'] = 'b3';
        $_COOKIE['a4'] = 'b4';
        $_ENV['a5'] = 'b5';

        $request = $this::fromGlobals();

        $request->get('a0')->shouldBe('b0');
        $request->post('a1')->shouldBe('b1');
        $request->server('a2')->shouldBe('b2');
        $request->file('a3')->shouldBe('b3');
        $request->cookie('a4')->shouldBe('b4');
        $request->env('a5')->shouldBe('b5');
    }

    public function it_can_be_enriched_with_parameters()
    {
        $newRequest = $this->addParameters(new Map(['a' => 1, 'b' => 2]));
        $newRequest->param('a')->shouldBe(1);
        $newRequest->param('b')->shouldBe(2);
    }
}
