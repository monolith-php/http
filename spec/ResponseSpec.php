<?php namespace spec\Monolith\Http;

use Monolith\Http\Response;
use PhpSpec\ObjectBehavior;

class ResponseSpec extends ObjectBehavior {

    function let() {
        $this->beConstructedThrough('code200', ['body content']);
    }

    function it_is_initializable() {

        $this->shouldHaveType(Response::class);
    }

    function it_can_represent_a_200_response() {
        $response = $this::code200('body123');
        $response->code()->shouldBe('200');
        $response->body()->shouldBe('body123');
        $response->codeString()->shouldBe('200 OK');
    }
}
