<?php


namespace OSN\Framework\Exceptions;


use Exception;

class CommandNotFoundException extends Exception
{
    protected $message = 'Command not found.';
    protected $code = 127;
}
