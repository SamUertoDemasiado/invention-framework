<?php


namespace OSN\Framework\Exceptions;


use Exception;

class ArgumentException extends Exception
{
    protected $code = 3;
    protected $message = "There is an error with the arguments.";
}
