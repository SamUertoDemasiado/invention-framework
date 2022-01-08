<?php


namespace OSN\Framework\Exceptions;


use Exception;

class QueryException extends Exception
{
    protected $code = 6;
    protected $message = "There is an error with the query.";
}
