<?php namespace Monolith\Http;

final class FileError
{
    private int $code;

    private array $description = [
        0 => 'There is no error, the file uploaded with success.',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        3 => 'The uploaded file was only partially uploaded.',
        4 => 'No file was uploaded.',
        6 => 'Missing a temporary folder.',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. ',
    ];

    private function __construct(
        int $code
    ) {
        $this->code = $code;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function description()
    {
        if ( ! isset($this->description[$this->code])) {
            throw new NoDescriptionKnownForFileErrorCode($this->code);
        }
        return $this->description[$this->code];
    }

    static function fromCode(int $errorCode): ?self
    {
        if ($errorCode === 0) return null;
        
        return new static($errorCode);
    }
}