<?php

namespace OSN\Framework\Exceptions;

class MethodNotFoundException extends \Exception
{
    protected $code = 1;
    protected $message = 'The specified method was not found.';
}