<?php namespace spec\Monolith\Http;

use Monolith\Http\Ipv4;
use Monolith\Http\File;
use PhpSpec\ObjectBehavior;
use Monolith\Collections\Dictionary;

class RequestSpec extends ObjectBehavior
{
    function let()
    {
        $body = 'raw body';
        $get = Dictionary::empty();
        $input = Dictionary::empty();
        $server = Dictionary::empty();
        $files = Dictionary::empty();
        $cookies = Dictionary::empty();
        $env = Dictionary::empty();
        $headers = Dictionary::empty();

        $this->beConstructedWith($body, $get, $input, $server, $files, $cookies, $env, $headers);
    }

    function it_can_be_constructed_from_globals()
    {
        $_GET['a0'] = 'b0';
        $_POST['a1'] = 'b1';
        $_SERVER['a2'] = 'b2';
        $_SERVER['REQUEST_URI'] = 'my uri';
        $_SERVER['REQUEST_METHOD'] = 'my method';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_FILES = [
            'f1' => [
                'name' => 'original-file-name.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/phpXstHsn',
                'error' => 0,
                'size' => 52,
            ],
            'f2' => [
                'name' => 'second-original-file-name.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/second-phpXstHsn',
                'error' => 0,
                'size' => 53,
            ],
        ];
        $_COOKIE['a4'] = 'b4';
        $_ENV['a5'] = 'b5';

        if ( ! function_exists('getallheaders')) {
            $_SERVER['HTTP_test'] = 'cat';
        } else {
            header('test: cat');
        }

        $request = $this::fromGlobals();

        $request->get()->get('a0')->shouldBe('b0');
        $request->post()->get('a1')->shouldBe('b1');
        $request->server()->get('a2')->shouldBe('b2');

        /** @var File $file */
        $file = $request->files()->get('f1');
        $file->phpFileArray()->shouldBe($_FILES['f1']);
        $file->name()->shouldBe('original-file-name.txt');
        $file->mimeType()->shouldBe('text/plain');
        $file->serverTempName()->shouldBe('/tmp/phpXstHsn');
        $file->error()->shouldBe(null);
        $file->size()->bytes()->shouldBe(52);
        
        /** @var File $file */
        $file = $request->files()->get('f2');
        $file->phpFileArray()->shouldBe($_FILES['f2']);
        $file->name()->shouldBe('second-original-file-name.txt');
        $file->mimeType()->shouldBe('text/plain');
        $file->serverTempName()->shouldBe('/tmp/second-phpXstHsn');
        $file->error()->shouldBe(null);
        $file->size()->bytes()->shouldBe(53);

        $request->cookies()->get('a4')->shouldBe('b4');
        $request->env()->get('a5')->shouldBe('b5');
        $request->headers()->get('Test')->shouldBe('cat');
        $request->uri()->shouldBe('my uri');
        $request->method()->shouldBe('my method');

        $request->clientIP()->shouldHaveType(Ipv4::class);
        $request->clientIp()->toString()->shouldBe('127.0.0.1');
    }

    public function it_can_be_enriched_with_parameters()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $request = $this::fromGlobals();

        $request = $request->addAppParameters(Dictionary::of(['a' => 1, 'b' => 2]));
        $request->appParameters()->get('a')->shouldBe(1);
        $request->appParameters()->get('b')->shouldBe(2);

        $serialized = $request->serialize();

        $serialized['parameters']->shouldHaveCount(2);
    }

    public function it_can_provided_decoded_urls_without_query_strings()
    {
        $_SERVER['REQUEST_URI'] = 'my-uri';
        $request = $this::fromGlobals();
        $request->uri()->shouldBe('my-uri');

        $_SERVER['REQUEST_URI'] = 'again?dogs=cats';
        $request = $this::fromGlobals();
        $request->uri()->shouldBe('again');

        $_SERVER['REQUEST_URI'] = 'hats%20again?dogs=cats';
        $request = $this::fromGlobals();
        $request->uri()->shouldBe('hats again');
    }

    function it_can_provide_query_string_parameters()
    {
        $_SERVER['QUERY_STRING'] = 'cat=dog';

        $request = $this::fromGlobals();

        $request->urlParameters()->isEmpty()->shouldBe(false);
        $request->urlParameters()->get('cat')->shouldBe('dog');
    }

    function it_provides_an_empty_dictionary_when_theres_no_query_string_parameters()
    {
        $_SERVER['QUERY_STRING'] = '';
        $request = $this::fromGlobals();
        $request->urlParameters()->isEmpty()->shouldBe(true);
    }
}