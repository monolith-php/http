<?php namespace Monolith\Http;

final class File
{
    private array $phpFileArray;
    private string $name;
    private string $mimeType;
    private string $serverTempName;
    private ?FileError $error;
    private ByteSize $size;

    private function __construct(
        array $phpFileArray
    ) {
        $this->phpFileArray = $phpFileArray;
        $this->name = $phpFileArray['name'];
        $this->mimeType = $phpFileArray['type'];
        $this->serverTempName = $phpFileArray['tmp_name'];
        $this->error = FileError::fromCode($phpFileArray['error']);
        $this->size = ByteSize::fromBytes($phpFileArray['size']);
    }

    public function phpFileArray(): array 
    {
        return $this->phpFileArray;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }
    
    public function serverTempName(): string
    {
        return $this->serverTempName;
    }

    public function error(): ?FileError
    {
        return $this->error;
    }

    public function size(): ByteSize
    {
        return $this->size;
    }
    
    public static function fromRequest(array $request)
    {
        return new static($request);
    }
}