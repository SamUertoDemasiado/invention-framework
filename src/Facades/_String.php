<?php


namespace OSN\Framework\Facades;


use OSN\Framework\Core\Facade;

class _String extends Facade
{
    protected static string $className = \OSN\Framework\DataTypes\_String::class;

    public static function initFacade(...$args)
    {
        return [
            "argsConstructor" => array_slice($args, 0, 1),
            "args" => array_slice($args, 1),
        ];
    }
}
