<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\Facade;
use OSN\Framework\Exceptions\MethodNotFoundException;

class FunctionUtils extends Facade
{
    protected static string $className = \OSN\Framework\Utils\FunctionUtils::class;
    protected static bool $init = false;

    public static function init()
    {
        self::$object = new static::$className(...self::$args);
    }

    /**
     * @throws MethodNotFoundException
     */
    public static function __callStatic($name, $arguments)
    {
        self::$args = $arguments;
        self::init();
        return parent::__callStatic($name, $arguments);
    }
}
