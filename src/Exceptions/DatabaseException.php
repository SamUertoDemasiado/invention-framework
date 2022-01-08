<?php


namespace OSN\Framework\Exceptions;


use Exception;

class DatabaseException extends Exception
{
    protected $code = 6;
    protected $message = "There is an error with the database.";
}
