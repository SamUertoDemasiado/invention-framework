<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\Facade;
use OSN\Framework\Exceptions\MethodNotFoundException;

class FunctionUtils extends Facade
{
    protected static string $className = \OSN\Framework\Utils\FunctionUtils::class;
}
