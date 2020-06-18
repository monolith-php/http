<?php namespace spec\Monolith\Http;

use PhpSpec\ObjectBehavior;
use Monolith\Http\FileError;

class FileErrorSpec extends ObjectBehavior
{
    function it_supports_the_eight_php_error_codes()
    {
        # no error generated for zero
        $error = FileError::fromCode(0);
        expect($error)->shouldBeNull();

        # no 5 apparently
        $errorCodes = range(1, 8);
        unset($errorCodes[4]);

        foreach ($errorCodes as $code) {
            $error = expect(
                FileError::fromCode($code)
            );

            $error->description()->shouldBeString();
            $error->code()->shouldBe($code);
        }
    }
}