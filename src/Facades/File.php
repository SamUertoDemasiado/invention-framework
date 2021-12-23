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
    protected static bool $init = false;

    public static function init()
    {
        self::$object = new self::$className(...self::$args);
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
