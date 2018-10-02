<?php namespace spec\Monolith\Http;

use Monolith\Http\Ipv6;
use Monolith\Http\Ipv6AddressIsNotValid;
use PhpSpec\ObjectBehavior;

class Ipv6Spec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('1200:0000:AB00:1234:0000:2552:7777:1313');
    }

    function it_represents_a_valid_ipv6_address()
    {
        $this->shouldHaveType(Ipv6::class);
    }

    function it_cannot_represent_an_invalid_ipv6_address()
    {
        $this->beConstructedWith('127.0.0.1');
        $this->shouldThrow(Ipv6AddressIsNotValid::class)->duringInstantiation();
    }

    function it_can_validate_a_valid_ipv6_address()
    {
        $this::isValid('1200:0000:AB00:1234:0000:2552:7777:1313')->shouldBe(true);
        $this::isValid('1.2.3.4')->shouldBe(false);
    }

    function it_can_compare_ipv6_addresses()
    {
        $this->equals(new Ipv6('1200:0000:AB00:1234:0000:2552:7777:1313'))->shouldBe(true);
        $this->equals(new Ipv6('1200:0000:AB00:1234:0000:2552:7777:1314'))->shouldBe(false);
    }

    function it_can_return_the_string_form_of_the_ipv6_address()
    {
        $this->toString()->shouldBe('1200:0000:AB00:1234:0000:2552:7777:1313');
    }
}
