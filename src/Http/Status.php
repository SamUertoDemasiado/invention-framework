<?php


namespace OSN\Framework\Http;


class Status
{
    use ResponseTrait;

    public function __construct(int $code)
    {
        $this->setCode($code);
    }
}
