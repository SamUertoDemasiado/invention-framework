<?php


namespace OSN\Framework\Core;


use Closure;
use OSN\Framework\Http\Request;

abstract class Middleware
{
    abstract public function handle(Request $request);
}
