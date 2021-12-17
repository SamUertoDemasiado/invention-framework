<?php


namespace OSN\Framework\Facades;


use App\Core\App;
use OSN\Envoy\ParseENV;
use OSN\Framework\Core\Facade;

class Database extends Facade
{
    protected static string $className = \App\Core\Database::class;

    public static function init()
    {
        self::$object = new static::$className(App::$app->env);
    }
}