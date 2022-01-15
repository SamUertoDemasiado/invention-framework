<?php

namespace OSN\Framework\Exceptions;

use Throwable;

class PropertyNotFoundException extends \Exception
{
    protected $code = 2;
    protected $message = 'The specified property was not found';
    protected $property;

    public function __construct($message = "", $property = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($property != null) {
            $this->property = $property;
            $this->message .= " '$property'";
        }
    }
}
