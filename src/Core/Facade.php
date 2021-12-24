<?php

namespace OSN\Framework\Core;

use OSN\Framework\Exceptions\MethodNotFoundException;
use OSN\Framework\Utils\FunctionUtils;
use ReflectionException;

/**
 * Class Facade
 * @package OSN\Framework\Core
 */
class Facade
{
    protected static string $className;
    protected static object $object;
    protected static bool $init = true;
    protected static bool $override = false;
    protected static bool $respectConstructor = true;

    public static function init($args)
    {
        self::$object = new static::$className(...$args);
    }

    /**
     * @throws MethodNotFoundException|ReflectionException
     */
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(static::$className, '__construct') && static::$respectConstructor) {
            $constructor = new FunctionUtils(static::$className, '__construct');
            $args = $constructor->getParameterTypes();
            $argsConstructor = array_slice($arguments, 0, count($args));
            $argsToPass = array_slice($arguments, count($args) === 0 ? 0 : count($args) - 1);
        }
        else {
            $argsConstructor = [];
            $argsToPass = $arguments;
        }

        if (static::$init)
            static::init($argsConstructor);

        if (method_exists(static::$object, $name) || method_exists(static::$object, "__call")) {
            return call_user_func_array([self::$object, $name], !static::$override ? $argsToPass : $arguments);
        }
        else {
            throw new MethodNotFoundException();
        }
    }
}
