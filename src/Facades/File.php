<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\Facade;
use OSN\Framework\Exceptions\MethodNotFoundException;

/**
 * @method static exists(string $string)
 */
class File extends Facade
{
    protected static string $className = \OSN\Framework\Files\File::class;

    public static function initFacade(...$args): array
    {
        return [
            "argsConstructor" => array_slice($args, 0, 1),
            "args" => array_slice($args, 1),
        ];
    }
}
