<?php namespace spec\Monolith\Http;

use PhpSpec\ObjectBehavior;
use Monolith\Http\ByteSizeIsTooLarge;

class ByteSizeSpec extends ObjectBehavior
{
    function it_takes_bytes()
    {
        $this->beConstructedThrough('fromBytes', [1]);
        $this->bytes()->shouldBe(1);
    }

    function it_takes_kilobytes()
    {
        $this->beConstructedThrough('fromKilobytes', [1]);
        $this->bytes()->shouldBe(1024);
    }

    function it_takes_megabytes()
    {
        $this->beConstructedThrough('fromMegabytes', [1]);
        $this->kilobytes()->shouldBe(1024.0);
    }

    function it_takes_gigabytes()
    {
        $this->beConstructedThrough('fromGigabytes', [1]);
        $this->megabytes()->shouldBe(1024.0);
    }

    function it_takes_terabytes()
    {
        $this->beConstructedThrough('fromTerabytes', [1]);
        $this->gigabytes()->shouldBe(1024.0);
    }

    function it_takes_petabytes()
    {
        $this->beConstructedThrough('fromPetabytes', [1]);
        $this->terabytes()->shouldBe(1024.0);
    }

    function it_cannot_handle_numbers_that_are_too_large()
    {
        $this->beConstructedThrough('fromPetabytes', [100000000000]);
        $this->shouldThrow(ByteSizeIsTooLarge::class)->duringInstantiation();
    }

    function it_gives_bits()
    {
        $this->beConstructedThrough('fromBytes', [1]);
        $this->bits()->shouldBe(8);
    }


    function it_gives_bytes()
    {
        $this->beConstructedThrough('fromBytes', [1]);
        $this->bytes()->shouldBe(1);
    }

    function it_gives_kilobytes()
    {
        $this->beConstructedThrough('fromMegabytes', [1]);
        $this->kilobytes()->shouldBe(1024.0);
    }

    function it_gives_megabytes()
    {
        $this->beConstructedThrough('fromGigabytes', [1]);
        $this->megabytes()->shouldBe(1024.0);
    }

    function it_gives_gigabytes()
    {
        $this->beConstructedThrough('fromTerabytes', [1]);
        $this->gigabytes()->shouldBe(1024.0);
    }

    function it_gives_terabytes()
    {
        $this->beConstructedThrough('fromPetabytes', [1]);
        $this->terabytes()->shouldBe(1024.0);
    }

    function it_gives_petabytes()
    {
        $this->beConstructedThrough('fromTerabytes', [1]);
        $this->petabytes()->shouldBe(1 / 1024);
    }
}