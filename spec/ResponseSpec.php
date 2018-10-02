<?php namespace spec\Monolith\Http;

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
}
