<?php namespace spec\Monolith\Http;

use PhpSpec\ObjectBehavior;

class FileSpec extends ObjectBehavior
{
    private $fileArray = [
        'name' => 'original-file-name.txt',
        'type' => 'text/plain',
        'tmp_name' => '/tmp/phpXstHsn',
        'error' => 0,
        'size' => 52,
    ];
    
    private $erroredFileArray = [
        'name' => 'original-file-name.txt',
        'type' => 'text/plain',
        'tmp_name' => '/tmp/phpXstHsn',
        'error' => 2,
        'size' => 52,
    ];

    function it_parses_requests()
    {
        $this->beConstructedThrough('fromRequest', [$this->fileArray]);
        
        $this->phpFileArray()->shouldBe($this->fileArray);
        $this->name()->shouldBe('original-file-name.txt');
        $this->mimeType()->shouldBe('text/plain');
        $this->serverTempName()->shouldBe('/tmp/phpXstHsn');
        $this->error()->shouldBe(null);
        $this->size()->bytes()->shouldBe(52);
    }
    
    function it_parses_errors() {
        $this->beConstructedThrough('fromRequest', [$this->erroredFileArray]);
        
        $this->phpFileArray()->shouldBe($this->erroredFileArray);
        $this->name()->shouldBe('original-file-name.txt');
        $this->mimeType()->shouldBe('text/plain');
        $this->serverTempName()->shouldBe('/tmp/phpXstHsn');
        $this->error()->code()->shouldBe(2);
        $this->size()->bytes()->shouldBe(52);
    }
}