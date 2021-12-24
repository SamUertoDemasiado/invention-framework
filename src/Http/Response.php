<?php


namespace OSN\Framework\Http;


class Response
{
    use ResponseTrait;

    protected ?string $response;
    protected int $code;
    protected array $headers;
    protected string $httpVersion = '1.1';

    public function __construct(?string $response = null, int $code = 200, array $headers = [])
    {
        $this->setContent($response);
        $this->setCode($code);
        $this->setStatusFromCode($code);
        $this->setHeaders($headers);
    }

    protected function setData()
    {
        header("HTTP/{$this->httpVersion} {$this->getCode()} {$this->getStatusText()}");
        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }
    }

    public function __toString()
    {
        $this->setData();
        return $this->getContent();
    }

    public function __invoke(): string
    {
        $this->setData();
        return $this->getContent();
    }

    public function redirect($url, int $code = 301)
    {
        $this->setCode($code);
        header("Location: $url");
    }
}
