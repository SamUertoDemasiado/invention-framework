<?php


namespace OSN\Framework\Core;


use Closure;
use OSN\Framework\Http\Request;

abstract class Middleware
{
    protected static bool $disabled = false;

    abstract public function handle(Request $request);

    public function execute(Request $request)
    {
        if (!static::$disabled)
            return $this->handle($request);
        return null;
    }

    public static function disable()
    {
        static::$disabled = false;
    }
}
