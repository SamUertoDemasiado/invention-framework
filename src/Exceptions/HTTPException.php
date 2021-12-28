<?php


namespace OSN\Framework\Exceptions;

use Exception;
use OSN\Framework\Http\ResponseTrait;
use OSN\Framework\Http\Status;
use Throwable;

class HTTPException extends Exception
{
    protected $code = 500;
    protected array $headers;

    public function __construct($code = 500, $message = 'Internal Server Error', array $headers = [], Throwable $previous = null)
    {
        $status = new Status($code);
        $message = $status->getStatusFromCode($code);
        parent::__construct($message, $code, $previous);
        $this->headers = $headers;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
