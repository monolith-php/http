<?php namespace spec\Monolith\Http;

use Monolith\Http\Ipv4;
use Monolith\Http\Ipv4AddressIsNotValid;
use PhpSpec\ObjectBehavior;

class Ipv4Spec extends ObjectBehavior {

    function let() {
        $this->beConstructedWith('127.0.0.1');
    }

    function it_represents_a_valid_ipv4_address() {

        $this->beConstructedWith('127.0.0.1');
        $this->shouldHaveType(Ipv4::class);
    }

    function it_cannot_represent_an_invalid_ipv4_address() {
        $this->beConstructedWith('1200:0000:AB00:1234:0000:2552:7777:1313');
        $this->shouldThrow(Ipv4AddressIsNotValid::class)->duringInstantiation();
    }

    function it_can_validate_a_valid_ipv4_address() {
        $this::isValid('1.2.3.4')->shouldBe(true);
        $this::isValid('1200:0000:AB00:1234:0000:2552:7777:1313')->shouldBe(false);
    }

    function it_can_compare_ipv4_addresses() {
        $this->equals(new Ipv4('127.0.0.1'))->shouldBe(true);
        $this->equals(new Ipv4('127.0.0.2'))->shouldBe(false);
    }

    function it_can_return_the_string_form_of_the_ipv4_address() {
        $this->toString()->shouldBe('127.0.0.1');
    }
}
