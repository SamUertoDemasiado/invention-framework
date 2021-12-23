<?php


namespace OSN\Framework\Exceptions;


use Exception;

class FactoryLimitException extends Exception
{
    protected $code = 4;
    protected $message = 'Max limit of the execution is exceeded.';
}