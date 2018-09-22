<?php namespace spec\Monolith\Http;

use Monolith\Http\Response;
use PhpSpec\ObjectBehavior;

class ResponseSpec extends ObjectBehavior {

    function let() {
        $this->beConstructedThrough('ok', ['body content']);
    }

    function it_is_initializable() {

        $this->shouldHaveType(Response::class);
    }

//    function it_can_represent_a_200_ok_response() {
//
//        $this->beConstructedThrough('ok', ['body123']);
//        $this->matches($this, '200', '200 OK', 'body123');
//    }
//
//    /**
//     * @param $response
//     */
//    private function matches($response, $code, $codeString, $body): void {
//
//        $response->code()->shouldBe($code);
//        $response->codeString()->shouldBe($codeString);
//        $response->body()->shouldBe($body);
//    }
}
