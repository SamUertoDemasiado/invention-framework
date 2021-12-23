<?php

namespace OSN\Framework\Core;

use OSN\Framework\Exceptions\MethodNotFoundException;

/**
 * Class Facade
 * @package OSN\Framework\Core
 */
class Facade
{
    protected static string $className;
    protected static object $object;
    protected static bool $init = true;
    protected static array $args = [];

    public static function init()
    {
        self::$object = new static::$className();
    }

    /**
     * @throws MethodNotFoundException
     */
    public static function __callStatic($name, $arguments)
    {
        if (static::$init)
            static::init();

        if (!method_exists(self::$object, $name) && !method_exists(self::$object, "__call")) {
            throw new MethodNotFoundException();
        }

        return call_user_func_array([self::$object, $name], $arguments);
    }
}
