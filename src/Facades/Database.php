<?php


namespace OSN\Framework\Facades;


use OSN\Framework\Core\App;
use OSN\Framework\Core\Facade;

class Database extends Facade
{
    protected static string $className = \OSN\Framework\Core\Database::class;

    public static function init($args)
    {
        self::$object = App::db();
    }
}
