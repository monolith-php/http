<?php namespace spec\Monolith\Http;

use Monolith\Http\Cookie;
use Monolith\Http\Response;
use PhpSpec\ObjectBehavior;
use Monolith\Collections\Dictionary;

class ResponseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedThrough('ok', ['body content']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Response::class);
    }

    function it_can_add_cookies()
    {
        $this->beConstructedThrough('ok', ['']);

        $newResponse = $this
            ->withCookie(new Cookie('session_id', '12', strtotime('now + 10 minutes'), '/', '', false, false))
            ->withCookie(new Cookie('user_id', '33', strtotime('now + 10 minutes'), '/', '', false, false));

        $cookie = $newResponse->cookies()[0];
        $cookie->shouldHaveType(Cookie::class);
        $cookie->name()->shouldBe('session_id');
        $cookie->value()->shouldBe('12');

        $cookie = $newResponse->cookies()[1];
        $cookie->shouldHaveType(Cookie::class);
        $cookie->name()->shouldBe('user_id');
        $cookie->value()->shouldBe('33');
    }

    function it_can_redirect()
    {
        $this->beConstructedThrough('redirect', ['/login']);
        $this->headers()->get('Location')->shouldBe('/login');
    }

    function it_can_add_headers()
    {
        $this->beConstructedThrough('ok', ['']);

        $newResponse = $this->withHeader('hats', 'tractor')
                            ->withHeader('cats', 'chica');

        /** @var Dictionary $header */
        $newResponse->headers()->get('hats')->shouldBe('tractor');
        $newResponse->headers()->get('cats')->shouldBe('chica');
    }

    function it_can_generate_a_stream_response()
    {
        $count = new CountStub(0);
        $streamFunction = function () use ($count) {
            $count->increment();
        };

        $this->beConstructedThrough('stream', [$streamFunction]);

        $this->send();

        expect($count->number())->shouldBe(1);
    }
}
