<?php

namespace OSN\Framework\Facades;

use App\Core\App;
use OSN\Framework\Core\Facade;

/**
 * @method get(string $route, array $array)
 * @method post(string $route, array $array)
 * @method put(string $route, array $array)
 * @method patch(string $route, array $array)
 * @method delete(string $route, array $array)
 */
class Router extends Facade
{
    public static function init()
    {
        self::$object = App::$app->router;
    }
}