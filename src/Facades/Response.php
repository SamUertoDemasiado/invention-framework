<?php

namespace OSN\Framework\Facades;

use OSN\Framework\Core\App;
use OSN\Framework\Core\Facade;

class Response extends Facade
{
    public static function init($args)
    {
        self::$object = App::response();
    }
}
