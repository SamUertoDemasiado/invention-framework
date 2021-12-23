<?php


namespace OSN\Framework\Exceptions;


use Exception;

class ModelException extends Exception
{
    protected $code = 5;
    protected $message = 'There is an error in the model.';
}
