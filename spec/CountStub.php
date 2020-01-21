<?php namespace spec\Monolith\Http;

final class CountStub
{
    /**
     * @var int
     */
    private $number;

    public function __construct($number = 0)
    {
        $this->number = $number;
    }

    public function increment()
    {
        $this->number++;
    }

    public function number()
    {
        return $this->number;
    }
}