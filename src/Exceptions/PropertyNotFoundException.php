<?php

namespace OSN\Framework\Exceptions;

class PropertyNotFoundException extends \Exception
{
    protected $code = 2;
    protected $message = 'The specified property was not found.';
}