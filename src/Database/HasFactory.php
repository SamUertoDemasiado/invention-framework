<?php


namespace OSN\Framework\Database;


use OSN\Framework\Facades\File;

trait HasFactory
{
    protected static ?string $factory = null;

    public static function setFactory(?string $factory = null)
    {
        if ($factory === null) {
            $array = explode('\\', self::class);
            $modelName = trim(end($array));
            $factoryName = "Database\\Factories\\" . $modelName . 'Factory';

            if (!class_exists($factoryName))
                return;

            self::$factory = $factoryName;
        }
        else {
            self::$factory = $factory;
        }
    }

    public static function factory(): Factory
    {
        self::setFactory();
        return new self::$factory();
    }
}
