<?php namespace spec\Monolith\Http;

use Monolith\Http\Cookie;
use Monolith\Http\Response;
use PhpSpec\ObjectBehavior;

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

    function it_can_set_cookies()
    {
        $this->beConstructedThrough('ok', ['']);

        $newResponse = $this->addCookie(new Cookie('session_id', '12', strtotime('now + 10 minutes'), '/', '', false, false));

        $cookie = $newResponse->cookies()[0];
        $cookie->shouldHaveType(Cookie::class);
        $cookie->name()->shouldBe('session_id');
        $cookie->value()->shouldBe('12');
    }

    function it_can_redirect()
    {
        $this->beConstructedThrough('redirect', ['/login']);
        $this->additionalHeaders()->get('Location')->shouldBe('/login');
    }
}
