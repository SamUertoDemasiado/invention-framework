<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\Facade;

class Response extends Facade
{
    protected static string $className = \OSN\Framework\Http\Response::class;
    protected static bool $respectConstructor = false;
}
