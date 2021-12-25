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
     * @throws MethodNotFoundException
     */
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(static::$className, '__construct') && method_exists(static::class, 'initFacade')) {
            $params = static::initFacade(...$arguments);
            $argsConstructor = $params['argsConstructor'];
            $argsToPass = $params['args'];
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
